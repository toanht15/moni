<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_get_instagram_hashtag_edit_modal extends BrandcoGETActionBase {
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    public function doAction() {

        $params = array(
            'instagram_hashtag_action_id' => $this->GET['instagram_hashtag_action_id'],
            'instagram_hashtag_user_post_id' => $this->GET['instagram_hashtag_user_post_id']
        );

        if ($this->GET['page_type']) {
            $params['page_type'] = $this->GET['page_type'];
        }

        $html = aafwWidgets::getInstance()->loadWidget('EditInstagramHashtagFormModal')->render($params);

        $json_data = $this->createAjaxResponse('ok', array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}