<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');

class SearchInstagramHashtagValidator extends BaseValidator {

    private $search_condition;

    public function __construct($search_condition, $search_type, $nullable = false) {
        parent::__construct();
        $this->search_condition = $search_condition;
        $this->search_type = $search_type;
        $this->nullable = $nullable;
    }

    public function validate() {
        if ($this->nullable && !$this->search_condition) {
            return true;
        }
        $action_id = explode('/', $this->search_type)[1];
        if ($this->search_type === CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION . '/' . $action_id) {
            if (!$this->search_condition) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION . '/' . $action_id][] = "1つ以上選択してください。";
            }
            return;
        }

        if ($this->search_type === CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME . '/' . $action_id) {
            if (!$this->search_condition) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME.'/'.$action_id][] = "1つ以上選択してください。";
            }
            return;
        }

        if ($this->search_type === CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS . '/' . $action_id) {
            if (!$this->search_condition) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS . '/' . $action_id][] = "1つ以上選択してください。";
            }
            return;
        }
    }
}
