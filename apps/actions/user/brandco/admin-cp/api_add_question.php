<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');
AAFW::import('jp.aainc.classes.entities.CpAction');

class api_add_question extends BrandcoGETActionBase {

    protected $AllowContent = array('JSON');
    public $NeedOption = array();

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

    public function validate() {
        return true;
    }

    function doAction() {

        $question = new QuestionnaireQuestions();
        $question->id = -$this->question_next_id;
        $question->type_id = $this->type;
        $question->question = '';
        $question->requirement_flg = CpQuestionnaireService::QUESTION_REQUIRED;

        $parser = new PHPParser();
        $data['add_question'] = true;
        $data['action_status'] = CpAction::STATUS_DRAFT;
        $html = $parser->parseTemplate(
            'CpQuestionnaireQuestion.php',
            array(
                'question' => $question,
                'action_data' => $data,
            )
        );

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';

    }
}
