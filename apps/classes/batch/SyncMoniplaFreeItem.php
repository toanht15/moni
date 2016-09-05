<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');

/**
 * monipla_free_item_dataを
 * monipla_free_item_relationsを介し
 * monipla_free_item_data_syncsへデータを整形しセットする
 *
 * Class SyncMoniplaFreeItem
 */
class SyncMoniplaFreeItem extends BrandcoBatchBase {

    protected $logger;
    /** @var MoniplaFreeItemService $monipla_free_item_service */
    protected $monipla_free_item_service;
    /** @var CpQuestionnaireService $questionnaire_service */
    protected $questionnaire_service;

    public function executeProcess() {
        $this->monipla_free_item_service = $this->service_factory->create('MoniplaFreeItemService');
        $this->questionnaire_service = $this->service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        $monipla_free_item_choice_syncs = $this->monipla_free_item_service->getMoniplaFreeItemChoiceSyncs();
        foreach ($monipla_free_item_choice_syncs as $monipla_free_item_choice_sync) {

            $choice_answers = $this->questionnaire_service->getSingleAndMultiChoiceAnswer($monipla_free_item_choice_sync->brands_users_relation_id,
                                                                                            $monipla_free_item_choice_sync->questionnaires_questions_relation_id);
            if($choice_answers) {
                if(strtotime($choice_answers->current()->updated_at) < strtotime($monipla_free_item_choice_sync->user_free_item_updated)) {
                    foreach($choice_answers as $choice_answer) {
                        $this->questionnaire_service->deleteChoiceAnswer($choice_answer);
                    }
                }
            }

            $this->questionnaire_service->setQuestionChoiceAnswer($monipla_free_item_choice_sync->questionnaires_questions_relation_id,
                                                                    $monipla_free_item_choice_sync->brands_users_relation_id,
                                                                    '',
                                                                    $monipla_free_item_choice_sync->choice_id,
                                                                    $monipla_free_item_choice_sync->answer_text);
        }

        $monipla_free_item_free_syncs = $this->monipla_free_item_service->getMoniplaFreeItemFreeSyncs();
        foreach ($monipla_free_item_free_syncs as $monipla_free_item_free_sync) {

            $free_answers = $this->questionnaire_service->getFreeAnswer($monipla_free_item_free_sync->brands_users_relation_id,
                                                                        $monipla_free_item_free_sync->questionnaires_questions_relation_id);
            if($free_answers) {
                if($free_answers && strtotime($free_answers->updated_at) < strtotime($monipla_free_item_free_sync->user_free_item_updated)) {
                    $this->questionnaire_service->deleteFreeAnswer($free_answers);
                } else {
                    continue;
                }
            }

            $this->questionnaire_service->setQuestionFreeAnswer($monipla_free_item_free_sync->questionnaires_questions_relation_id,
                                                                $monipla_free_item_free_sync->brands_users_relation_id,
                                                                '',
                                                                $monipla_free_item_free_sync->answer_text);
        }
    }
}
