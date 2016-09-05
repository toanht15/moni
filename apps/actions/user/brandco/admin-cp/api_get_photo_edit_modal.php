<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_get_photo_edit_modal extends BrandcoGETActionBase {
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        $params = array('photo_action_id' => $this->GET['photo_action_id'], 'photo_user_id' => $this->GET['photo_user_id']);
        if ($this->GET['page_type']) {
            $params['page_type'] =  $this->GET['page_type'];
        }

        $html = aafwWidgets::getInstance()->loadWidget('EditPhotoFormModal')->render($params);

        $json_data = $this->createAjaxResponse('ok', array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}