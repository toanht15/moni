<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_validate_mail_signup extends BrandcoPOSTActionBase {
    public $NeedOption = array();
    public $CsrfProtect = true;

    protected $ContainerName = 'api_validate_mail_signup';
    protected $AllowContent = array('JSON');
    protected $ValidatorDefinition = array(
        'mail_address' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
            'validator' => array('MailAddress')
        ),
        'password' => array(
            'required' => 1,
            'type' => 'str',
            'length' => array(
                'min' => 8
            ),
            'validator' => array('Alnum')
        )
    );

    public function validate() {
        if ($this->isLogin()) {
            $this->Validator->setError('already_login', '1');
        }

        /** @var BrandcoAuthService $brandco_auth_service */
        $brandco_auth_service = $this->getService('BrandcoAuthService');

        // AAIDにアカウントが存在するか検証
        $result = $brandco_auth_service->getUsersByMailAddress($this->POST['mail_address']);
        if ($result->result->status !== Thrift_APIStatus::SUCCESS) {
            return false;
        }

        if ($result->user[0]->user) {
            $this->Validator->setError('password', 'EXISTED_MAIL_ADDRESS');
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
        $json_data = $this->createAjaxResponse("ok", array(), array());
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}
