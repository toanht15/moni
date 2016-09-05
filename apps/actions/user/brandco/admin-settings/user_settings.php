<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class user_settings extends BrandcoPOSTActionBase {
    protected $ContainerName = 'user_settings';
    protected $Form = array(
        'package' => 'admin-settings',
        'action' => 'user_settings_form',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'restricted_age' => array(
            'type' => 'num'
        ),
        'not_authentication_url' => array(
            'type' => 'str',
            'length' => 512,
            'validator' => array('URL')
        )
    );

    public function doThisFirst() {
        $this->Form['action'] = 'user_settings_form?mid=failed';
    }

    public function validate () {
        // 代理店はユーザ設定させない
        $manager = $this->getManager();
        if ($manager && $manager->authority == Manager::AGENT) {
            return false;
        }

        if (in_array('privacy_required_restricted', $this->privacy)) {
            if (!in_array('privacy_required_birthday', $this->privacy)) {
                $this->Validator->setError('privacy_required_birthday', 'admin-settings.user_settings_form.privacy_required_birthday');
            } else {
                $cp_flow_service = $this->getService('CpFlowService');
                $cps = $cp_flow_service->getOpenCpsByBrandId($this->getBrand()->id);

                foreach ($cps as $cp) {
                    if ($cp->restricted_age_flg && $cp->restricted_age < $this->restricted_age) {
                        $this->Validator->setError('restricted_age', 'BRAND_RESTRICTED_AGE_NOT_MATCH');
                        break;
                    }
                }
                
                if($this->age_authentication_flg && !$this->not_authentication_url){
                    $this->Validator->setError('not_authentication_url', 'NOT_INPUT_TEXT');
                }
            }
        }

        return $this->Validator->isValid();
    }

    function doAction() {

        /** @var BrandPageSettingService $page_setting_service */
        $page_setting_service = $this->createService('BrandPageSettingService');

        if($this->POST['mode'] == BrandPageSettingService::MODE_PRIVACY) {
            $page_setting_service->setRequiredPrivacySettings($this->brand->id, $this->privacy, $this->privacy_address);
            $page_setting_service->setRestrictedAgeSettings($this->brand->id, $this->restricted_age);

            $page_setting_service->setAgeAuthenticationFlgSettings($this->brand->id,$this->age_authentication_flg ? 1 : 0);
            $page_setting_service->setNotAuthenticationUrlSettings($this->brand->id,$this->not_authentication_url);

        } else if($this->POST['mode'] == BrandPageSettingService::MODE_AGREEMENT) {

            $show_agreement_checkbox = null;

            //マネジャー権限でログインした場合、利用規約のチェックボックスの表示をセットできる
            if ($this->isLoginManager()) {
                $show_agreement_checkbox = $this->show_agreement_checkbox ? BrandPageSetting::SHOW_AGREEMENT_CHECKBOX : BrandPageSetting::NOT_SHOW_AGREEMENT_CHECKBOX;
            }
            $page_setting_service->setAgreementSettings($this->brand->id, $this->agreement, $show_agreement_checkbox);
        }

        return 'redirect: '.Util::rewriteUrl('admin-settings', 'user_settings_form', array(), array('mid'=>'updated'));
    }
}
