<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpUserQuestionnaireAnswer extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = $this->getService('CpQuestionnaireService');
        // アンケートの設問を並び順通りに取得
        $params['questionnaire_action'] = $cp_questionnaire_service->getCpQuestionnaireAction($params['display_action_id']);
        if($params['questionnaire_action']->id) {
            $params['questionnaires_questions_relations'] = $cp_questionnaire_service->getRelationsByQuestionnaireActionId($params['questionnaire_action']->id);
            foreach($params['questionnaires_questions_relations'] as $relation) {
                $params['questions'][$relation->id] = $cp_questionnaire_service->getQuestionById($relation->question_id);
            }
        }
        if($params['fan_list_users']) {
            $user_ids = array();
            foreach($params['fan_list_users'] as $fan_list_user) {
                $user_ids[] = $fan_list_user->user_id;
            }
            /** @var CpUserListService $cpUserListService */
            $cpUserListService = $this->getService('CpUserListService');
            $params['fan_list_question'] = $cpUserListService->getFanListQuestion($user_ids, $params['questions'], $params['brand']->id);
        }

        $service_factory = new aafwServiceFactory();
        /** @var $brand_user_relation_service BrandsUsersRelationService */
        $params['brand_user_relation_service'] = $service_factory->create('BrandsUsersRelationService');

        return $params;
    }

}