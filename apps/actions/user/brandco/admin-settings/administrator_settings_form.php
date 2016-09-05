<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class administrator_settings_form extends BrandcoGETActionBase {
    protected $ContainerName = 'administrator_settings';
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        // 代理店は管理者追加できたらダメ
        $manager = $this->getManager();
        if ($manager && $manager->authority == Manager::AGENT) {
            return false;
        }

        return true;
    }

    function doAction() {

        /** @var UserService $users_service */
        $users_service = $this->createService('UserService');
        $this->Data['admin_user'] = $users_service->getAdminUsers($this->Data['brand']->id);

        $this->Data['loginUserId'] = $this->getBrandsUsersRelation()->user_id;

        return 'user/brandco/admin-settings/administrator_settings_form.php';
    }
}
