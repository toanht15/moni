<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_get_questionnaire_answer_list extends BrandcoGETActionBase {
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        $html = aafwWidgets::getInstance()->loadWidget('CpQuestionnaireList')->render(array(
            'cp_id' => $this->GET['cp_id'],
            'brand_id' => $this->GET['brand_id'],
            'action_id' => $this->GET['action_id'],
            'page' => $this->GET['page'],
            'targeted_question_ids' => $this->GET['targeted_question_ids'],
            'approval_status' => $this->GET['approval_status'],
            'order_kind' => $this->GET['order_kind'],
            'order_type' => $this->GET['order_type'],
            'page_limit' => $this->GET['page_limit']
        ));

        $json_data = $this->createAjaxResponse('ok', array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}