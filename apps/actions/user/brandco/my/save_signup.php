<?php
AAFW::import('jp.aainc.classes.brandco.auth.BrandcoAuthActionBase');

class save_signup extends BrandcoAuthActionBase {

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
        ),
    );

    function getAuthAction() {
        return self::AUTH_ACTION_SIGNUP;
    }

    function validate() {
        $result = $this->getUserByMailAddress($this->POST['mail_address']);
        if ($result->user) {
            $this->Validator->setError('password', 'EXISTED_MAIL_ADDRESS');
        }

        return $this->Validator->isValid();
    }

    function doSubAction() {
        // AAIDを作成
        $result = $this->createAAIDByMailAddressAndPassword($this->POST['mail_address'], $this->POST['password']);
        if (!$result) {
            aafwLog4phpLogger::getDefaultLogger()->error("Thrift api error: can't create 'aaid account'. (mail_address = {$this->POST['mail_address']})");
            return '500';
        }
        // UserIDの取得
        $userId = $this->getMoniplaUserIdByMailAddressAndPassword($this->POST['mail_address'], $this->POST['password']);
        if (!$userId) {
            aafwLog4phpLogger::getDefaultLogger()->error("Thrift api error: can't get 'user id'. (mail_address = {$this->POST['mail_address']})");
            return '500';
        }
        // 認証コードの作成: Platform
        $code = $this->createAuthorizationCodeByMoniplaUserIdAndClientId($userId, ApplicationService::CLIENT_ID_PLATFORM);
        if (!$code) {
            aafwLog4phpLogger::getDefaultLogger()->error("Thrift api error: can't get 'authorization code' (monipla_user_id = {$userId}, client_id = platform).");
            return '500';
        }
        // 認証コードの作成: Brandco
        $code = $this->createAuthorizationCodeByMoniplaUserIdAndClientId($userId, ApplicationService::CLIENT_ID_BRANDCO);
        if (!$code) {
            aafwLog4phpLogger::getDefaultLogger()->error("Thrift api error: can't get 'authorization code' (monipla_user_id = {$userId}, client_id = brandco).");
            return '500';
        }

        return 'redirect: ' . Util::rewriteUrl('auth', 'signup', array(), array('code' => $code,'mail_login'=>'true'));
    }
}
