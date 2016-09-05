<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_change_action_panel_hidden_flg extends BrandcoPOSTActionBase {
    protected $ContainerName = 'photo_campaign';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        $cp_photo_action_service = $this->createService('CpPhotoActionService');

        $cp_photo_action = $cp_photo_action_service->getCpPhotoAction($this->POST['action_id']);
        if ($cp_photo_action->panel_hidden_flg != $this->POST['panel_hidden_flg']) {
            $cp_photo_action->panel_hidden_flg = $this->POST['panel_hidden_flg'];
            $cp_photo_action_service->updateCpPhotoAction($cp_photo_action);
        }

        $json_data = $this->createAjaxResponse('ok');
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}