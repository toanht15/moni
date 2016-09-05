<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_get_pre_login_form extends BrandcoPOSTActionBase {
    public $NeedOption = array();
    protected $ContainerName = 'api_get_pre_login_form';
    protected $AllowContent = array('JSON');
    protected $ValidatorDefinition = array();

    public function doThisFirst() {
        if ($this->hasAdminInviteToken($this->getBrand()->id)) {
            $this->NeedPublic = true;
        }
    }

    public function validate() {
        $this->Validator = new aafwValidator();
        if ($this->isLogin()) {
            $this->Validator->setError('already_login', '1');

            return false;
        }

        return true;
    }

    public function getFormURL () {
        $errors = array();
        foreach ($this->Validator->getError() as $key => $value) {
            $errors[$key] = $this->Validator->getMessage($key);
        }
        $json_data = $this->createAjaxResponse("ng", array('redirect_url' => Util::rewriteUrl('', '')), $errors);
        $this->assign('json_data', $json_data);

        return false;
    }

    public function doAction() {
        $php_parser = new PHPParser();
        $html = Util::sanitizeOutput($php_parser->parseTemplate('auth/PreLoginForm.php', array(
            'mail_address' => $this->mail_address,
            'page_type' => $this->page_type
        )));

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function hasAdminInviteToken($brand_id) {
        $admin_invite_token_service = $this->getService('AdminInviteTokenService');
        return $admin_invite_token_service->getValidInvitedToken($brand_id);
    }
}
