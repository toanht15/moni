<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_verify_mail_address extends BrandcoPOSTActionBase {
    public $NeedOption = array();
    public $CsrfProtect = true;
    
    protected $ContainerName = 'api_verify_mail_address';
    protected $AllowContent = array('JSON');
    protected $ValidatorDefinition = array(
        'mail_address' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
            'validator' => array('MailAddress')
        )
    );

    private $mail_address;

    public function doThisFirst() {
        $this->mail_address = $this->POST['mail_address'];

        if ($this->hasAdminInviteToken($this->getBrand()->id)) {
            $this->NeedPublic = true;
        }
    }

    public function validate() {
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
        /** @var BrandcoAuthService $brandco_auth_service */
        $brandco_auth_service = $this->getService('BrandcoAuthService');
        $php_parser = new PHPParser();

        $params = array(
            'ActionForm' => array('mail_address' => $this->mail_address),
            'pageStatus' => array('brand' => $this->getBrand()),
            'page_type' => $this->POST['page_type']
        );

        $result = $brandco_auth_service->getUsersByMailAddress($this->mail_address);
        if ($result->user) {
            if ($result->user[0]->enabledPassword) {
                $html = Util::sanitizeOutput($php_parser->parseTemplate('auth/LoginForm.php', $params));
            } else {
                $html = Util::sanitizeOutput($php_parser->parseTemplate('auth/LoginGuide.php', $params));
            }
        } else {
            $html = Util::sanitizeOutput($php_parser->parseTemplate('auth/SignupForm.php', $params));
        }

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