<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
class reset_password_form extends BrandcoManagerGETActionBase {

    protected $ContainerName = 'reset_password';

    public $NeedManagerLogin = false;

    public function beforeValidate () {
        $this->resetValidateError();

        if (!$this->getActionContainer('Errors')) {
            $this->Data['mode'] = '';
        } else {
            $this->Data['mode'] = ManagerService::CHANGE_ERROR;
        }
    }

    /**
     * @return bool
     */

    public function validate () {

        $manager_service = $this->getService('ManagerService');

        $decoded_token = base64_decode($this->GET['token']);
        $token = json_decode($decoded_token);
        $manager= $manager_service->getManagerById($token->id);

        if($manager)
        {
            $this->Data['get_user_information'] = $manager;
        } else {
            $this->Data['mode'] = ManagerService::ACCOUNT_ERROR;
        }

        //パスワード有効期限チェック
        if( date("Y-m-d H:i:s",strtotime("-1 week")) >= $token->date
            || $token->date != $manager->login_try_reset_date){
            // 有効期限切れのページへ（期限：１週間）
            return '404';
        }

        return true;
    }

    function doAction() {

        //viewに渡すデータ
        return 'manager/account/reset_password_form.php';
    }
}