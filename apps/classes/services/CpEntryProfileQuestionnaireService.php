<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class CpEntryProfileQuestionnaireService extends aafwServiceBase {

    protected $questionnaire;

    public function __construct() {
        $this->questionnaire = $this->getModel('CpProfileQuestionnaires');
    }

    public function getQuestionnairesByCpActionId($cp_action_id) {
        if (Util::isNullOrEmpty($cp_action_id)) {
            return array();
        }
        $filter = array(
            'conditions' => array(
                'cp_action_id' => $cp_action_id
            ),
        );
        return $this->questionnaire->find($filter);
    }

    public function getQuestionnairesByProfileQuestionnaireId($profile_questionnaire_id) {
        if (Util::isNullOrEmpty($profile_questionnaire_id)) {
            return array();
        }
        $filter = array(
            'conditions' => array(
                'profile_questionnaire_id' => $profile_questionnaire_id
            ),
        );
        return $this->questionnaire->find($filter);
    }

    public function convertQuestionnairesToMap($questionnaires) {
        if ($questionnaires === null) {
            return array();
        }
        $entry_questionnaires = array();
        foreach ($questionnaires as $qst) {
            $entry_questionnaires[$qst->profile_questionnaire_id] = 1;
        }
        return $entry_questionnaires;
    }

    public function countQuestionnairesByCpActionId($cp_action_id) {
        if (Util::isNullOrEmpty($cp_action_id)) {
            return 0;
        }
        $filter = array(
            'conditions' => array(
                'cp_action_id' => $cp_action_id
            ),
        );
        return $this->questionnaire->count($filter);
    }

    public function hasEntryQuestionnaire($entry_questionnaires) {
        if ($entry_questionnaires === null) {
            return false;
        }
        return count($entry_questionnaires) > 0;
    }

    public function clearQuestionnairesByCpActionId($cp_action_id) {
        if (Util::isNullOrEmpty($cp_action_id)) {
            return;
        }
        foreach ($this->getQuestionnairesByCpActionId($cp_action_id) as $qst) {
            $this->questionnaire->deletePhysical($qst);
        }
    }

    public function deleteEntryQuestionnairesByProfileQuestionnaireId($profile_questionnaire_id) {
        if (Util::isNullOrEmpty($profile_questionnaire_id)) {
            return;
        }
        foreach ($this->getQuestionnairesByProfileQuestionnaireId($profile_questionnaire_id) as $entry_questionnaire) {
            $this->questionnaire->delete($entry_questionnaire);
        }
    }

    public function addQuestionnaire($cp_action_id, $questionnaire_id) {
        if (Util::existNullOrEmpty($cp_action_id, $questionnaire_id)) {
            throw new aafwException("$cp_action_id and $questionnaire_id mustn't be null or empty!");
        }
        $new_qst = $this->questionnaire->createEmptyObject();
        $new_qst->cp_action_id = $cp_action_id;
        $new_qst->profile_questionnaire_id = $questionnaire_id;
        return $this->questionnaire->save($new_qst);
    }

    public function copyQuestionnaire($old_profile_questionnaire, $new_cp_action_id) {
        $new_profile_questionnaire = $this->questionnaire->createEmptyObject();
        $new_profile_questionnaire->profile_questionnaire_id = $old_profile_questionnaire->profile_questionnaire_id;
        $new_profile_questionnaire->cp_action_id = $new_cp_action_id;
        $this->questionnaire->save($new_profile_questionnaire);
    }
}