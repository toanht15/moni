<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class signup_mail_settings extends BrandcoPOSTActionBase {

    protected $ContainerName = 'signup_mail_settings';
    protected $Form = array(
        'package' => 'admin-settings',
        'action' => 'signup_mail_settings_form?mid=failed',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'sender_name' => array(
            'type' => 'str',
            'length' => 255
        ),
        'subject' => array(
            'type' => 'str',
            'length' => 255
        ),
        'body_plain' => array(
            'type' => 'str'
        )
    );

    public function doThisFirst() {

        if($this->POST['send_signup_mail_flg']){

            $this->ValidatorDefinition['sender_name']['required'] = true;
            $this->ValidatorDefinition['subject']['required'] = true;
            $this->ValidatorDefinition['body_plain']['required'] = true;

        }

    }

    public function validate () {
        return true;
    }

    function doAction() {

        $pageSettingService = $this->createService('BrandPageSettingService');

        $pageSettingService->setCustomMailSettings($this->brand->id,$this->POST['send_signup_mail_flg'] ? 1 : 0);

        /** @var BrandCustomMailTemplateService $brandCustomMailTemplateService */
        $brandCustomMailTemplateService = $this->createService('BrandCustomMailTemplateService');

        $brandCustomMailTemplateService->setCustomMailTemplate($this->brand->id, $this->POST);

        return 'redirect: '.Util::rewriteUrl('admin-settings', 'signup_mail_settings_form', array(), array('mid'=>'updated'));
    }
}
