<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class signup_mail_settings_form extends BrandcoGETActionBase {

    protected $ContainerName = 'signup_mail_settings';
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        $this->deleteErrorSession();
        return true;
    }

    function doAction() {

        if(!$this->Data['pageStatus']['can_set_sign_up_mail']){
            return '404';
        }

        $brand = $this->getBrand();

        /** @var BrandCustomMailTemplateService $brandCustomMailTemplateService */
        $brandCustomMailTemplateService = $this->createService('BrandCustomMailTemplateService');
        $customMailTemplate = $brandCustomMailTemplateService->getBrandCustomMailByBrandId($brand->id);

        $actionForm = $customMailTemplate ? $customMailTemplate->toArray() : array();

        $actionForm['send_signup_mail_flg'] = BrandInfoContainer::getInstance()->getBrandPageSetting()->send_signup_mail_flg;

        $this->assign('ActionForm', $actionForm);

        return 'user/brandco/admin-settings/signup_mail_settings_form.php';
    }
}
