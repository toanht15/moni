<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class delete_administrator_form extends BrandcoGETActionBase{
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function doThisFirst () {
        $this->Data['user_id'] = $this->admin_uid;
    }

    public function validate () {
        return true;
    }

    function doAction() {

        $user_service = $this->createService('UserService');
        $this->Data['userInfo'] = $user_service->getUserByBrandcoUserId($this->Data['user_id']);

        return 'user/brandco/admin-settings/delete_administrator_form.php';
    }
}
