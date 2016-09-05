<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class CpActions extends aafwEntityStoreBase {

    protected $_TableName = 'cp_actions';
    protected $_EntityName = "CpAction";

    public static function loadActionSpecificCatalogs($cp_action_map) {
        $table_names = array();
        foreach ($cp_action_map as $id => $row) {
            if ($row->type === CpAction::TYPE_SHIPPING_ADDRESS) {
                $table_names[] = 'cp_shipping_address_actions';
                $table_names[] = 'cp_profile_questionnaires';
                $table_names[] = 'prefectures';
                $table_names[] = 'regions';
                $table_names[] = 'shipping_addresses';
            } else if ($row->type === CpAction::TYPE_MESSAGE) {
                $table_names[] = 'cp_message_actions';
            } else if ($row->type === CpAction::TYPE_QUESTIONNAIRE) {
                $table_names[] = 'cp_questionnaire_actions';
                $table_names[] = 'questionnaires_questions_relations';
                $table_names[] = 'questionnaire_questions';
                $table_names[] = 'question_choice_requirements';
                $table_names[] = 'question_choices';
                $table_names[] = 'question_choice_answers';
                $table_names[] = 'question_free_answers';
            } else if ($row->type === CpAction::TYPE_SHARE) {
                $table_names[] = 'cp_share_actions';
                $table_names[] = 'cp_share_user_logs';
                $table_names[] = 'user_applications';
                $table_names[] = 'facebook_streams';
                $table_names[] = 'facebook_entries';
                $table_names[] = 'crawler_hosts';
                $table_names[] = 'crawler_types';
                $table_names[] = 'crawler_urls';
            } else if ($row->type === CpAction::TYPE_INSTANT_WIN) {
                $table_names[] = 'cp_instant_win_actions';
                $table_names[] = 'instant_win_prizes';
                $table_names[] = 'brandco_social_accounts';
                $table_names[] = 'instant_win_users';
                $table_names[] = 'instant_win_user_logs';
            }
        }
        if (count($cp_action_map) === 1 && array_values($cp_action_map)[0]->isLegalOpeningCpAction()) {
            $table_names[] = 'user_attributes';
            $table_names[] = 'user_search_info';
            $table_names[] = 'shipping_addresses';
            $table_names[] = 'profile_questionnaires_questions_relations';
            $table_names[] = 'profile_questionnaire_questions';
            $table_names[] = 'profile_question_choice_requirements';
            $table_names[] = 'profile_question_choices';
            $table_names[] = 'profile_question_choice_answers';
            $table_names[] = 'profile_question_free_answers';
            $table_names[] = 'profile_choice_answer_histories';
            $table_names[] = 'profile_free_answer_histories';
            $table_names[] = 'prefectures';
            $table_names[] = 'regions';
        }
        if (count($table_names) > 0) {
            aafwEntityStoreBase::loadCatalogs($table_names);
        }
    }
}