<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');

class QuestionnaireUserAnswerService extends aafwServiceBase {

    public function __construct() {
        $this->user_answers = $this->getModel('QuestionnaireUserAnswers');
    }

    /**
     *
     */
    public function createEmptyObject() {
        $this->user_answers->createEmptyObject();
    }

    /**
     * @param $user_answer
     */
    public function saveQuestionnaireUserAnswer($user_answer) {
        $this->user_answers->save($user_answer);
    }

    /**
     * @param $bur_id
     * @param $cp_action_id
     * @return mixed
     */
    public function getUserAnswersByBurIdAndActionId($bur_id, $cp_action_id) {
        $filter = array(
            'brands_users_relation_id' => $bur_id,
            'cp_action_id' => $cp_action_id
        );

        return $this->user_answers->findOne($filter);
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getUserAnswersByCpActionId($cp_action_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id
        );

        return $this->user_answers->find($filter);
    }

    /**
     * @param $cp_action_id
     * @param $approval_status
     * @return mixed
     */
    public function countQuestionnaireAnswerByCpActionId($cp_action_id, $approval_status) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'approval_status' => $approval_status
        );

        return $this->user_answers->count($filter);
    }

    /**
     * @param $cp_action_id
     * @throws Exception
     */
    public function deletePhysicalUserAnswersByCpActionId($cp_action_id) {
        if (!$cp_action_id) {
            throw new Exception("QuestionnaireUserAnswerService#deletePhysicalUserAnswerByCpActionId cp_action_id null");
        }

        $questionnaire_user_answers = $this->getUserAnswersByCpActionId($cp_action_id);
        if ($questionnaire_user_answers) {
            foreach ($questionnaire_user_answers as $questionnaire_user_answer) {
                $this->user_answers->deletePhysical($questionnaire_user_answer);
            }
        }
    }

    public function deletePhysicalUserAnswerByBurIdAndCpActionId($brands_users_relation_id, $cp_action_id) {
        if (!$brands_users_relation_id || !$cp_action_id) {
            throw new Exception("QuestionnaireUserAnswerService#deletePhysicalUserAnswerByBurIdAndCpActionId brands_users_relation_id = {$brands_users_relation_id}, cp_action_id = {$cp_action_id}");
        }

        $questionnaire_user_answer = $this->getUserAnswersByBurIdAndActionId($brands_users_relation_id, $cp_action_id);
        if ($questionnaire_user_answer) {
            $this->user_answers->deletePhysical($questionnaire_user_answer);
        }
    }
}
