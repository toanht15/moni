<?php
AAFW::import ( 'jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase' );
class api_record_panel_click extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_record_panel_click';
    protected $AllowContent = array('JSON');
    public $NeedOption = array();

    public $CsrfProtect = true;

    public function beforeValidate() {
    }

    public function validate() {
        return true;
    }

    function doAction() {
        $user_id = $this->getBrandsUsersRelation()->user_id;

        /** @var UserPanelClickService $user_panel_click_service */
        $user_panel_click_service = $this->getService('UserPanelClickService');
        $user_panel_click_service->setPanelClick($this->entry, $this->entry_id, $user_id, $this->type);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}