<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');

/**
 * monipla_free_item_dataを
 * monipla_free_item_relationsを介し
 * monipla_free_item_data_syncsへデータを整形しセットする
 *
 * Class CreateMoniplaFreeItem
 */
class CreateMoniplaFreeItemChoiceRelation extends BrandcoBatchBase {

    /** @var MoniplaFreeItemService $monipla_free_item_service */
    protected $monipla_free_item_service;
    /** @var CpQuestionnaireService $questionnaire_service */
    protected $questionnaire_service;

    public function executeProcess() {
        if(!$this->argv['brand_id']) {
            echo '「brand_id=」の形式で引数を入力してください。'.PHP_EOL;
            return;
        }
        $this->monipla_free_item_service = $this->service_factory->create('MoniplaFreeItemService');
        $this->questionnaire_service = $this->service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        $questionnaires_questions_relations = $this->questionnaire_service->getPublicProfileQuestionRelationByBrandId($this->argv);
        foreach ($questionnaires_questions_relations as $questionnaires_questions_relation) {
            if(!$questionnaires_questions_relation->public) {
                continue;
            }
            $profile_question_choices = $this->questionnaire_service->getChoicesByQuestionId($questionnaires_questions_relation->question_id);
            if(!$profile_question_choices) {
                continue;
            }
            foreach ($profile_question_choices as $profile_question_choice) {
                $this->monipla_free_item_service->setMoniplaFreeItemChoiceRelations($profile_question_choice);
            }
        }
    }
}
