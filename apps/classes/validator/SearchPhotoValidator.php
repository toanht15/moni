<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');

class SearchPhotoValidator extends BaseValidator {

    private $search_condition;

    public function __construct($search_condition, $search_type, $nullable) {
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
        if ($this->search_type === CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS.'/'.$action_id) {
            if ($this->isValidPhotoShareSns()) {
                return;
            } else {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS.'/'.$action_id][] = "1つ以上選択してください。";
                return;
            }
        }

        if ($this->search_type === CpCreateSqlService::SEARCH_PHOTO_SHARE_TEXT.'/'.$action_id) {
            if (!$this->search_condition) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PHOTO_SHARE_TEXT.'/'.$action_id][] = "1つ以上選択してください。";
            }
            return;
        }

        if ($this->search_type === CpCreateSqlService::SEARCH_PHOTO_APPROVAL_STATUS.'/'.$action_id) {
            if (!$this->search_condition) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PHOTO_APPROVAL_STATUS.'/'.$action_id][] = "1つ以上選択してください。";
            }
            return;
        }
    }

    private function isValidPhotoShareSns() {
        foreach ($this->search_condition as $key => $value) {
            if (!preg_match('/^switch_type\//', $key)) {
                return true;
            }
        }
        return false;
    }
}
