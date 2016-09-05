<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class user_settings_form extends BrandcoGETActionBase {
    protected $ContainerName = 'user_settings';
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate () {
        // 代理店はユーザ設定させない
        $manager = $this->getManager();
        if ($manager && $manager->authority == Manager::AGENT) {
            return 403;
        }

        $this->deleteErrorSession();
        return true;
    }

    function doAction() {

        if($this->getActionContainer('Errors')) {
            if ($this->GET['f'] && $this->GET['f'] == 'pq') {
                $this->Data['profile_question_error'] = true;
            }
            $this->SESSION['UserSettingError'] = $this->getActionContainer('Errors');
            $this->SESSION['UserSettingValidateError'] = $this->getActionContainer('ValidateError');
        } else {
            $this->SESSION['UserSettingError'] = null;
            $this->SESSION['UserSettingValidateError'] = null;
        }

        /** @var BrandPageSettingService $pageSettingsService */
        $pageSettingsService = $this->createService('BrandPageSettingService');
        $pageSettings = $pageSettingsService->getPageSettingsByBrandId($this->Data['brand']->id);
        if ($pageSettings) {
            $pageSettings = $pageSettings->toArray();
        }

        /** @var ProfileQuestionnaireService $profileQuestionnairesService */
        $profileQuestionnairesService = $this->createService('ProfileQuestionnaireService');
        $profile_questionnaires = $profileQuestionnairesService->getAllProfileQuestionByBrandId($this->Data['brand']->id);
        $this->Data['profile_questionnaires'] = $profile_questionnaires;

        $form = array();
        foreach($pageSettings as $key => $value) {
            if($value) {
                $form['privacy'][] = $key;
                if ($key == "privacy_required_address") {
                    $form["privacy_address"] = $value;
                }
            }
        }

        if (!$form["privacy_address"]) {
            $form["privacy_address"] = BrandPageSetting::GET_ALL_ADDRESS;
        }

        $form['restricted_age'] = $pageSettings['restricted_age'];

        $brandGlobalSettingService = $this->getService('BrandGlobalSettingService');
        $brandGlobalSetting = $brandGlobalSettingService->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::AUTHENTICATION_PAGE);

        $this->Data['can_set_authentication_page'] = !Util::isNullOrEmpty($brandGlobalSetting);

        $form['age_authentication_flg'] = $pageSettings['age_authentication_flg'];
        $form['not_authentication_url'] = $pageSettings['not_authentication_url'];
        $form['authentication_page_content'] =  $pageSettings['authentication_page_content'];

        $authenticationPageUrl = Util::rewriteUrl('', 'authentication_page' , array(), array('preview' => BrandPageSettingService::AUTHENTICATION_PAGE_DEFAULT_PREVIEW_MODE));
        $this->Data['authentication_page_preview_url'] = Util::rewriteUrl('', 'authentication_page_preview', array(), array('preview_url' => base64_encode($authenticationPageUrl)));

        $form['agreement'] = $pageSettings['agreement'];

        //ログイン制限を使えるかどうかチェックする
        $can_use_login_limit_setting = $brandGlobalSettingService->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_USE_LOGIN_LIMIT_SETTING);
        $this->Data['can_use_login_limit_setting'] = Util::isNullOrEmpty($can_use_login_limit_setting) ? false : true;

        if($this->Data['can_use_login_limit_setting']){
            $brand_login_setting_service = $this->getService('BrandLoginSettingService', array($this->getBrand()->id));
            $form['brand_login_snses'] = $brand_login_setting_service->getBrandLoginSnsList();
        }
        //利用規約への同意を確認するチェックボックスを表示すかどうか
        $form['show_agreement_checkbox'] = $pageSettings['show_agreement_checkbox'];

        $brand_login_setting_service = $this->getService('BrandLoginSettingService', array($this->getBrand()->id));
        $form['brand_login_snses'] = $brand_login_setting_service->getBrandLoginSnsList();

        $this->assign('ActionForm', $form);

        return 'user/brandco/admin-settings/user_settings_form.php';
    }
}
