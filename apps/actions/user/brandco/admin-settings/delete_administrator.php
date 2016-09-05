<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class delete_administrator extends BrandcoPOSTActionBase {
    protected $ContainerName = 'delete_administrator';
    protected $Form = array(
        'package' => 'admin-settings',
        'action' => 'delete_administrator_form',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate() {
        return true;
    }

    function doAction() {

        // 管理者フラグの削除処理
        $brands_users_relation_service = $this->createService('BrandsUsersRelationService');
        $brands_users_relation_service->deleteAdminFlg($this->brand->id, $this->admin_uid);

        return 'redirect: ' . Util::rewriteUrl('admin-settings', 'administrator_settings_form', array(), array('close' => 1, 'refreshTop' => 1));
    }
}
