<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_get_tweet_list extends BrandcoGETActionBase {
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        $html = aafwWidgets::getInstance()->loadWidget('CpTweetList')->render(array(
            'cp_id' => $this->GET['cp_id'],
            'action_id' => $this->GET['action_id'],
            'page' => $this->GET['page'],
            'tweet_status' => $this->GET['tweet_status'],
            'approval_status' => $this->GET['approval_status'],
            'order_kind' => $this->GET['order_kind'],
            'order_type' => $this->GET['order_type'],
        ));

        $json_data = $this->createAjaxResponse('ok', array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}