<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');

class SearchQuestionnaireValidator extends BaseValidator {

    private $search_condition;

    public function __construct($search_condition, $search_type, $nullable = false) {
        parent::__construct();
        $this->search_condition = $search_condition;
        $this->search_type = $search_type;
        $this->nullable = $nullable;
    }

    public function validate() {
        if(preg_match('/^'.CpCreateSqlService::SEARCH_QUESTIONNAIRE.'\//', $this->search_type)) {
            list($search_questionnaire, $question_id) = explode('/', $this->search_type);
            $exist = false;
            foreach($this->search_condition as $key=>$value) {
                if(!preg_match('/^switch_type\//', $key)) {
                    $exist = true;
                }
            }
            if(!$this->nullable && !$exist) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_QUESTIONNAIRE.'/'.$question_id][] = "1つ以上選択してください。";
                return;
            }
        }
    }
}
