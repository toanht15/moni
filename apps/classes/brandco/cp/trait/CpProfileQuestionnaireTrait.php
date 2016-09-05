<?php
trait CpProfileQuestionnaireTrait {
    private $checked_profile_questionnaire_ids = array();

    private $is_opening_cp_action;
    private $prefill_flg;

    /**
     * @param $data
     */
    public function setCheckedProfileQuestionnaireIds($data) {
        foreach ($data as $key => $value) {
            if (strpos($key, 'entry_questionnaire') !== 0 || $value === '0') {
                continue;
            }

            $id = substr($key, strlen('entry_questionnaire'));
            $this->checked_profile_questionnaire_ids[] = $id;
        }
    }

    /**
     * @return bool
     */
    public function isValidChoice() {
        if (!$this->canUpdateQuestionnaires()) return true;

        if (count($this->checked_profile_questionnaire_ids) === 0 && $this->prefill_flg === '1') {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function canUpdateQuestionnaires() {
        if (!$this->isOpeningCpAction()) return false;

        return $this->getCpAction()->status != CpAction::STATUS_FIX || $this->getCp()->isDemo();
    }

    /**
     * @return mixed
     */
    public function isOpeningCpAction() {
        if (!isset($this->is_opening_cp_action)) {
            if (!$this->getCpAction()) {
                $this->is_opening_cp_action = false;
            } else {
                $this->is_opening_cp_action = $this->getCpAction()->isOpeningCpAction();
            }
        }

        return $this->is_opening_cp_action;
    }

    /**
     * @return array
     */
    public function getCheckedProfileQuestionnaireIds() {
        return $this->checked_profile_questionnaire_ids;
    }
}