<?php
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.services.QuestionQuestionService');
class CpQuestionnaireActionService extends aafwServiceBase {

    protected $questionnaire;

    public function __construct() {
        $this->questionnaire = $this->getModel('CpQuestionnaireActions');
    }

    /**
     * CPアクションIDよりアンケートアクションを取得
     * @param $cp_action_id
     */
    public function getCpQuestionnaireAction($cp_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_action_id' => $cp_action_id,
            ),
        );
        $cp_questionnaire_action = $this->questionnaire->findOne($filter);

        return $cp_questionnaire_action;
    }

    /**
     * @param $cp_questionnaire_action
     */
    public function updateCpQuestionnaireAction($cp_questionnaire_action) {
        $this->questionnaire->save($cp_questionnaire_action);
    }
}