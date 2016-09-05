<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
class reset_password extends BrandcoManagerPOSTActionBase {

    protected $ContainerName = 'reset_password';
    protected $Form = array(
        'package' => 'account',
        'action' => 'reset_password_form',
    );
    public $NeedManagerLogin = false;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'new_password' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 32,
            'validator' => array('AlnumSymbol')
        ),
        'new_password_confirm' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 32,
            'validator' => array('AlnumSymbol')
        ),
    );

    public function validate () {

        if($this->new_password_confirm != $this->new_password) {
            $this->Validator->setError('new_password_confirm', 'NOT_COLLECT_NEWPASSWORD');
        }
        return !$this->Validator->getErrorCount();
    }

    public function getFormURL() {
        return 'redirect: /account/reset_password_form?token=' . $this->POST['manager_token'];
    }

    public function doAction() {
        
        // パスワード変更処理
        /** @var ManagerService $managver_service */
        $manager_service = $this->createService('ManagerService');

        $decoded_token = base64_decode($this->POST['manager_token']);
        $token = json_decode($decoded_token);

        $manager = $manager_service->getManagerById($token->id);
        
        $manager_service->changeManagerPass($manager, md5($this->new_password));
        $manager_service->resetAccountLock($manager);

        return 'redirect: /account/reset_password_finish_form';

    }
}