<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_get_photo_list extends BrandcoGETActionBase {
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        $data = array(
            'cp_id' => $this->GET['cp_id'],
            'action_id' => $this->GET['action_id'],
            'page' => $this->GET['page'],
            'brand_id' => $this->getBrand()->id,
            'approval_status' => $this->GET['approval_status'],
            'order_kind' => $this->GET['order_kind'],
            'order_type' => $this->GET['order_type'],
            'limit' => $this->GET['limit']
        );
        $html = aafwWidgets::getInstance()->loadWidget('CpPhotoList')->render($data);
        $this->setSearchPhotoConditionSession($data);

        $json_data = $this->createAjaxResponse('ok', array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    /**
     * 写真の絞り込をセッションに保存する
     * @param $data
     */
    public function setSearchPhotoConditionSession($data) {
        $session = $this->getSearchConditionSession($data['cp_id']);
        $session['search_photo_campaign'.'/'.$data['action_id']] = $data;
        $this->setSearchConditionSession($data['cp_id'], $session);
    }
}