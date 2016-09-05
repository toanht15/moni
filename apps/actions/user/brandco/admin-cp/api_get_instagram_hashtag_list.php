<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_get_instagram_hashtag_list extends BrandcoGETActionBase {
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
            'approval_status' => $this->GET['approval_status'],
            'order_kind' => $this->GET['order_kind'],
            'order_type' => $this->GET['order_type'],
            'duplicate_flg' => $this->GET['duplicate_flg'],
            'reverse_post_time_flg' => $this->GET['reverse_post_time_flg']
        );
        $html = aafwWidgets::getInstance()->loadWidget('CpInstagramHashtagList')->render($data);
        $this->setSearchInstagramConditionSession($data);

        $json_data = $this->createAjaxResponse('ok', array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    /**
     * Instagram投稿の絞り込をセッションに保存する
     * @param $data
     */
    public function setSearchInstagramConditionSession($data) {
        $session = $this->getSearchConditionSession($data['cp_id']);
        $session['search_instagram_hashtag_campaign'.'/'.$data['action_id']] = $data;
        $this->setSearchConditionSession($data['cp_id'], $session);
    }

}