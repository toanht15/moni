<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class OpeningCpQuestionnaireActionForm extends aafwWidgetBase {

    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();
        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $params['cp_questionnaire_service'] = $service_factory->create('CpQuestionnaireService');

        // アンケートの設問を並び順通りに取得
        $params['questionnaire_action'] = $params['cp_questionnaire_service']->getCpQuestionnaireAction($params["cp_action"]->id);
        if($params['questionnaire_action']->id) {
            $params['questionnaire_question_relations'] = $params['cp_questionnaire_service']->getRelationsByQuestionnaireActionId($params['questionnaire_action']->id);
        }

        return $params;
    }
}