<?php
AAFW::import('jp.aainc.classes.BrandInfoContainer');

trait EditActionTrait {

    /**
     * @param $params
     * @return array
     */
    public function fetchQuestionnaires($params) {
        if (!$params['action']->isOpeningCpAction()) return array();

        $service_factory = new aafwServiceFactory();
        $brand = BrandInfoContainer::getInstance()->getBrand();

        /** @var ProfileQuestionnaireService $profileQuestionnairesService */
        $questionnaire_service = $service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        $existing_profile_questions = $questionnaire_service->getProfileQuestionsByBrandId($brand->id);
        if ($existing_profile_questions) {
            $params['profile_questionnaires'] = $existing_profile_questions;
        }

        $manager = $params['action']->getActionManagerClass();
        $opening_action = $manager->getConcreteAction($params['action']);

        /** @var CpEntryProfileQuestionnaireService $cp_profile_questionnaire_service */
        $cp_profile_questionnaire_service = $service_factory->create('CpEntryProfileQuestionnaireService');
        $entry_action_profile_questionnaires = $cp_profile_questionnaire_service->getQuestionnairesByCpActionId($opening_action->cp_action_id);
        $params['entry_questionnaires'] = array();
        if ($entry_action_profile_questionnaires) {
            foreach ($existing_profile_questions as $existing_profile_question) {
                foreach ($entry_action_profile_questionnaires as $eap_qst) {
                    if ($existing_profile_question->id === $eap_qst->profile_questionnaire_id) {
                        $params['entry_questionnaires'][$existing_profile_question->id] = 1;
                        break;
                    }
                }
            }
        }

        /** @var CpQuestionnaireService $questionnaire_service */
        $questionnaire_service = $this->getService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        $params['profile_questions_relations'] = $questionnaire_service->getPublicProfileQuestionRelationByBrandId($brand->id);

        return $params;
    }

    /**
     * @return mixed
     */
    public function hasFanList() {
        return BrandInfoContainer::getInstance()->getBrand()->hasOption(BrandOptions::OPTION_FAN_LIST, BrandInfoContainer::getInstance()->getBrandOptions());
    }

    /**
     * @return mixed
     */
    public function isDemo() {
        return $this->cp->isDemo();
    }
}