<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class MoniplaFreeItemService extends aafwServiceBase {

    protected $monipla_free_items;
    protected $monipla_free_item_relations;

    /** @var MoniplaFreeItemSyncs monipla_free_item_relations */
    protected $monipla_free_item_syncs;

    public function __construct() {
        /** @var MoniplaFreeItems monipla_free_items */
        $this->monipla_free_items = $this->getModel("MoniplaFreeItems");

        /** @var MoniplaFreeItemRelations monipla_free_item_relations */
        $this->monipla_free_item_relations = $this->getModel("MoniplaFreeItemRelations");

        /** @var MoniplaFreeItemFreeSyncs monipla_free_item_free_syncs */
        $this->monipla_free_item_free_syncs = $this->getModel("MoniplaFreeItemFreeSyncs");

        /** @var MoniplaFreeItemChoiceSyncs monipla_free_item_choice_syncs */
        $this->monipla_free_item_choice_syncs = $this->getModel("MoniplaFreeItemChoiceSyncs");

        /** @var MoniplaFreeItemChoiceRelations monipla_free_item_choice_relations */
        $this->monipla_free_item_choice_relations = $this->getModel("MoniplaFreeItemChoiceRelations");
    }

    public function getMoniplaFreeItemRelations() {
        return $this->monipla_free_item_relations->findAll();
    }

    public function createMoniplaFreeItemSync($monipla_free_item, $brands_users_relation_id, $brandco_free_item_id) {
        $monipla_free_item_sync = $this->monipla_free_item_syncs->createEmptyObject();
        $monipla_free_item_sync->relation_id = $brands_users_relation_id;
        $monipla_free_item_sync->question_id = $brandco_free_item_id;
        $monipla_free_item_sync->answer = $monipla_free_item->input_value;
        $this->monipla_free_item_syncs->save($monipla_free_item_sync);
    }

    public function getMoniplaFreeItemSync($relation_id, $question_id) {
        $filter = array(
            'relation_id' => $relation_id,
            'question_id' => $question_id
        );
        return $this->monipla_free_item_syncs->findOne($filter);
    }

    public function getMoniplaFreeItems() {
        return $this->monipla_free_items->find(array());
    }

    public function getMoniplaFreeItemSyncs() {
        return $this->monipla_free_item_syncs->find(array());
    }

    public function getMoniplaFreeItemChoiceSyncs() {
        return $this->monipla_free_item_choice_syncs->find(array());
    }

    public function getMoniplaFreeItemFreeSyncs() {
        return $this->monipla_free_item_free_syncs->find(array());
    }

    public function getChoiceByMoniplaFreeItem($brandco_free_item, $input_value) {
        $filter = array(
            'brandco_free_item' => $brandco_free_item,
            'input_value' => $input_value
        );
        return $this->monipla_free_item_choice_relations->findOne($filter);
    }

    /**
     * @param $questionnaires_questions_relation_id
     * @param $brands_users_relations_id
     * @param $choice_id
     * @param $answer_text
     * @return $monipla_free_item_choice_syncs
     */
    public function setMoniplaFreeItemChoiceSyncs($questionnaires_questions_relation_id, $brands_users_relation_id, $choice_id, $user_free_item_updated, $answer_text = '') {
        $free_item_choice_sync = $this->createEmptysetMoniplaFreeItemChoiceSyncs();
        $free_item_choice_sync->brands_users_relation_id = $brands_users_relation_id;
        $free_item_choice_sync->questionnaires_questions_relation_id = $questionnaires_questions_relation_id;
        $free_item_choice_sync->choice_id = $choice_id;
        $free_item_choice_sync->answer_text = $answer_text;
        $free_item_choice_sync->user_free_item_updated = $user_free_item_updated;

        return $this->createMoniplaFreeItemChoiceSyncs($free_item_choice_sync);
    }

    public function createMoniplaFreeItemChoiceSyncs($free_item_choice_sync) {
        return $this->monipla_free_item_choice_syncs->save($free_item_choice_sync);
    }

    public function createEmptysetMoniplaFreeItemChoiceSyncs() {
        return $this->monipla_free_item_choice_syncs->createEmptyObject();
    }

    /**
     * @param $questionnaires_questions_relation_id
     * @param $brands_users_relations_id
     * @param $answer_text
     * @return $monipla_free_item_free_syncs
     */
    public function setMoniplaFreeItemFreeSyncs($questionnaires_questions_relation_id, $brands_users_relation_id, $user_free_item_updated, $answer_text) {
        $free_item_free_sync = $this->createEmptysetMoniplaFreeItemFreeSyncs();
        $free_item_free_sync->brands_users_relation_id = $brands_users_relation_id;
        $free_item_free_sync->questionnaires_questions_relation_id = $questionnaires_questions_relation_id;
        $free_item_free_sync->answer_text = $answer_text;
        $free_item_free_sync->user_free_item_updated = $user_free_item_updated;

        return $this->createMoniplaFreeItemFreeSyncs($free_item_free_sync);
    }

    public function createMoniplaFreeItemFreeSyncs($free_item_free_sync) {
        return $this->monipla_free_item_free_syncs->save($free_item_free_sync);
    }

    public function createEmptysetMoniplaFreeItemFreeSyncs() {
        return $this->monipla_free_item_free_syncs->createEmptyObject();
    }

    /**
     * @param $profile_question_choice
     */
    public function setMoniplaFreeItemChoiceRelations($profile_question_choice) {
        $choice_relation = $this->createEmptyMoniplaFreeItemChoiceRelation();
        $choice_relation->brandco_free_item = $profile_question_choice->question_id;
        $choice_relation->input_value = $profile_question_choice->choice_num - 1;
        $choice_relation->choice_id = $profile_question_choice->id;
        $choice_relation->choice = $profile_question_choice->choice;

        $this->creatMoniplaFreeItemChoiceRelation($choice_relation);
    }

    public function creatMoniplaFreeItemChoiceRelation($choice_relation) {
        return $this->monipla_free_item_choice_relations->save($choice_relation);
    }

    public function createEmptyMoniplaFreeItemChoiceRelation() {
        return $this->monipla_free_item_choice_relations->createEmptyObject();
    }

}
