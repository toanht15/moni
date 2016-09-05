<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');

class api_add_choice extends BrandcoGETActionBase {

    protected $AllowContent = array('JSON');
    public $NeedOption = array();

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

    public function validate() {
        return true;
    }

    function doAction() {

        $choice = new QuestionChoices();
        $choice->id = -$this->choice_next_id;

        $question = new QuestionnaireQuestions();
        $question->id = $this->question_id;
        $question->type_id = $this->question_type;

        $parser = new PHPParser();
        $html = $parser->parseTemplate(
            'CpAddQuestionnaireChoice.php',
            array(
                'choice'   => $choice,
                'question' => $question,
            )
        );

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
