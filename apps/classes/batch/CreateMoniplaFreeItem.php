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
class CreateMoniplaFreeItem extends BrandcoBatchBase {

    protected $logger;
    /** @var MoniplaFreeItemService $monipla_free_item_service */
    protected $monipla_free_item_service;
    /** @var $user_service UserService */
    protected $user_service;
    /** @var BrandsUsersRelationService $brands_users_relation_service */
    protected $brands_users_relation_service;
    /** @var CpQuestionnaireService $questionnaire_service */
    protected $questionnaire_service;

    public function executeProcess() {
        $this->monipla_free_item_service = $this->service_factory->create('MoniplaFreeItemService');
        $this->user_service = $this->service_factory->create('UserService');
        $this->brands_users_relation_service = $this->service_factory->create('BrandsUsersRelationService');
        $this->questionnaire_service = $this->service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        // 関連テーブルから一覧取得
        $monipla_free_item_relations = $this->monipla_free_item_service->getMoniplaFreeItemRelations();

        $this->logger->info("monipla_free_item_relations: total = ". $monipla_free_item_relations->total());

        foreach ($monipla_free_item_relations as $monipla_free_item_relation) {

            $this->logger->info("monipla_free_item_relation: values=" . json_encode($monipla_free_item_relation->toArray(), JSON_PRETTY_PRINT));

            // 設問のタイプが選択肢か自由回答かで処理が異なる
            $profile_question = $this->questionnaire_service->getQuestionById($monipla_free_item_relation->brandco_free_item);

            $this->logger->info("profile_question: values=" . json_encode($profile_question->toArray(), JSON_PRETTY_PRINT));

            // 中間テーブルへの一時登録
            $items = $monipla_free_item_relation->getMoniplaFreeItems();

            $this->logger->info("getMoniplaFreeItems: total=" . $items->total());

            foreach ($items as $data) {

                $this->logger->info("data: values=" . json_encode($data->toArray(), JSON_PRETTY_PRINT));

                $this->createMoniplaFreeItemSync($monipla_free_item_relation, $data, $profile_question->type_id);
            }
        }
    }

    /**
     * relation情報とmoniplaのフリー項目のデータからsyncデータを作成する
     * @param $monipla_free_item_relation
     * @param $monipla_free_item
     * @param $type_id
     */
    private function createMoniplaFreeItemSync($monipla_free_item_relation, $monipla_free_item, $type_id) {

        $this->logger->info(
                "createMoniplaFreeItemSync:
                monipla_free_item_relation=" . json_encode($monipla_free_item_relation->toArray(), JSON_PRETTY_PRINT) .
                "monipla_free_item=" . json_encode($monipla_free_item->toArray(), JSON_PRETTY_PRINT) .
                "type_id={$type_id}");

        // ユーザ情報取得
        $user = $this->user_service->getUserByMoniplaUserId($monipla_free_item->platform_user_id);

        $this->logger->info("user: " . json_encode($user, JSON_PRETTY_PRINT));

        if(!$user->id) return;

        // brands_users_relation情報取得
        $brands_users_relation = $this->brands_users_relation_service->getBrandsUsersRelation($monipla_free_item->brand_id, $user->id);

        $this->logger->info("brands_users_relation: values=" . json_encode($brands_users_relation->toArray(), JSON_PRETTY_PRINT));

        if(!$brands_users_relation->id) return;

        $profile_question_relation = $this->questionnaire_service->getRelationByProfileQuestionId($monipla_free_item_relation->brandco_free_item);

        $this->logger->info("profile_question_relation: values=" . json_encode($profile_question_relation->toArray(), JSON_PRETTY_PRINT));

        // 選択肢の設問形式の場合
        $isChoiceQuestion = QuestionTypeService::isChoiceQuestion($type_id);

        $this->logger->info("isChoiceQuestion={$isChoiceQuestion}, type_id={$type_id}");

        if($isChoiceQuestion) {
            $question_choice_answers = $this->questionnaire_service->getSingleAndMultiChoiceAnswer($brands_users_relation->id, $profile_question_relation->id);

            $this->logger->info("question_choice_answers: total=" . $question_choice_answers->total());

            $question_choice_answers_current = $question_choice_answers->current();
            $check_date = ($question_choice_answers->total() == 0) || ($question_choice_answers && strtotime($monipla_free_item->user_free_item_updated) > strtotime($question_choice_answers_current->updated_at));
            $question_choice_answers_not_exists = !$question_choice_answers;
            $question_choice_answers_exists = $question_choice_answers == 1;
            $user_free_item_updated = strtotime($monipla_free_item->user_free_item_updated);
            $question_choice_answers_updated_at = strtotime($question_choice_answers_current->updated_at);

            $this->logger->info(
                "check_date={$check_date},
                {$question_choice_answers_not_exists}
                {$question_choice_answers_exists},
                {$user_free_item_updated},
                {$question_choice_answers_updated_at}");

            if($check_date) {
                if($monipla_free_item->input_value == '') return;
                $choice_relation = $this->monipla_free_item_service->getChoiceByMoniplaFreeItem($monipla_free_item_relation->brandco_free_item, $monipla_free_item->input_value);

                $this->logger->info("choice_relation=" . json_encode($choice_relation->toArray(), JSON_PRETTY_PRINT));

                $result = $this->monipla_free_item_service->setMoniplaFreeItemChoiceSyncs($profile_question_relation->id, $brands_users_relation->id, $choice_relation->choice_id, $monipla_free_item->user_free_item_updated);

                $this->logger->info("new monipla_free_item_choice_syncs: values=" . json_encode($result->toArray(), JSON_PRETTY_PRINT));
            }
        } else {
            $question_free_answer = $this->questionnaire_service->getFreeAnswersInSync($brands_users_relation->id, $profile_question_relation->id);

            $this->logger->info("question_free_answer: values=" . json_encode($question_free_answer->toArray(), JSON_PRETTY_PRINT));

            $is_writable = !$question_free_answer || ($question_free_answer && strtotime($monipla_free_item->user_free_item_updated) > strtotime($question_free_answer->updated_at));
            $question_free_answer_not_exists = !$question_free_answer;
            $question_free_answer_exists = $question_free_answer == 1;
            $user_free_item_updated = strtotime($monipla_free_item->user_free_item_updated);
            $question_free_answer_updated_at = strtotime($question_free_answer->updated_at);

            $this->logger->info(
                "is_writable = {$is_writable},
                {$question_free_answer_not_exists},
                {$question_free_answer_exists},
                {$user_free_item_updated},
                {$question_free_answer_updated_at}");
            if($is_writable) {
                $result = $this->monipla_free_item_service->setMoniplaFreeItemFreeSyncs($profile_question_relation->id, $brands_users_relation->id, $monipla_free_item->user_free_item_updated, $monipla_free_item->input_value);
                $this->logger->info("monipla_free_item_free_syncs: values=" . json_encode($result->toArray(), JSON_PRETTY_PRINT));

            }
        }
    }
}
