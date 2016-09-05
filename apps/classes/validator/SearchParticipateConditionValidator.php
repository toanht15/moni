<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');

class SearchParticipateConditionValidator extends BaseValidator {

    private $search_condition;
    private $nullable;

    public function __construct($search_condition, $search_type, $nullable = false) {
        parent::__construct();
        $this->search_condition = $search_condition;
        $this->search_type = $search_type;
        $this->nullable = $nullable;
    }

    public function validate() {
        $action_id = explode('/',$this->search_type)[1];
        if(!$this->nullable && !$this->search_condition) {
            $this->errors['searchError/'.CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$action_id][] = "1つ以上選択してください。";
            return;
        } else {
            foreach($this->search_condition as $key => $value) {
                // スピードくじの絞り込みを行う場合
                if($key == 'search_participate_condition/'.$action_id.'/'.CpCreateSqlService::PARTICIPATE_COUNT_INSTANT_WIN) {
                    if($this->search_condition['search_count_instant_win_from/'.$action_id] === '' && $this->search_condition['search_count_instant_win_to/'.$action_id] === '') {
                        $this->errors['searchError/'.CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$action_id][] = "参加回数の指定をした場合、<br>範囲を入力してください。";
                        return;
                    }
                    if(($this->search_condition['search_count_instant_win_from/'.$action_id] !== '' && $this->search_condition['search_count_instant_win_to/'.$action_id] !== '') &&
                        ($this->search_condition['search_count_instant_win_from/'.$action_id] > $this->search_condition['search_count_instant_win_to/'.$action_id])) {
                        $this->errors['searchError/'.CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$action_id][] = "参加回数の範囲の指定順序が正しくありません。";
                        return;
                    }
                    if($this->search_condition['search_count_instant_win_from/'.$action_id] !== '') {
                        if(!preg_match("/^[0-9]+$/",$this->search_condition['search_count_instant_win_from/'.$action_id])) {
                            $this->errors['searchError/'.CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$action_id][] = "半角数字で入力してください。";
                            return;
                        } elseif($this->search_condition['search_count_instant_win_from/'.$action_id] == 0) {
                            $this->errors['searchError/'.CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$action_id][] = "1以上の数字で入力してください。";
                            return;
                        }
                    }
                    if($this->search_condition['search_count_instant_win_to/'.$action_id] !== '') {
                        if(!preg_match("/^[0-9]+$/",$this->search_condition['search_count_instant_win_to/'.$action_id])) {
                            $this->errors['searchError/'.CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$action_id][] = "半角数字で入力してください。";
                            return;
                        } elseif($this->search_condition['search_count_instant_win_to/'.$action_id] == 0) {
                            $this->errors['searchError/'.CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$action_id][] = "1以上の数字で入力してください。";
                            return;
                        }
                    }
                }
            }
        }
    }
}
