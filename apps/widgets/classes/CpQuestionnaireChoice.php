<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');

class CpQuestionnaireChoice extends aafwWidgetBase {
    public function doService( $params = array() ){

        $params['requirement'] = new QuestionChoiceRequirements();
        $params['relation'] = new QuestionnairesQuestionsRelations();

        $params['has_error'] = false;
        $action_data = $params['action_data'];
        $params['errors'] = $action_data['cp_questionnaire_errors'] ? $action_data['cp_questionnaire_errors']->getErrors() : '';

        // エラーがあった場合、この設問で発生しているかどうか
        foreach($params['errors'] as $key => $value) {
            if($key == 'question_id_'.$params['question']->id || preg_match('/^choice_id_'.$params['question']->id.'_/', $key) || preg_match('/^choice_image_file_'.$params['question']->id.'_/', $key) || preg_match('/^pulldown_choice_'.$params['question']->id.'/', $key)) {
                $params['has_error'] = true;
            }
        }

        // 参加者一覧の「メッセージ作成」の方でエラーが発生した場合は、保存ができないのでセッションから取得
        if($params['errors']['is_fan_list_page']) {
            $choice_num = 1;
            foreach($action_data['ActionForm'] as $key=>$value) {
                if(preg_match('/^requirement_'.$params['question']->id.'/', $key)) {
                    $params['relation']->requirement_flg = $value;
                }
                if(preg_match('/^random_order_'.$params['question']->id.'/', $key)) {
                    $params['requirement']->random_order_flg = $value;
                }
                if(preg_match('/^multi_answer_'.$params['question']->id.'/', $key)) {
                    $params['requirement']->multi_answer_flg = $value;
                }
                if(preg_match('/^use_other_choice_'.$params['question']->id.'/', $key)) {
                    $params['requirement']->use_other_choice_flg = $value;
                }
                if(preg_match('/^choice_id_'.$params['question']->id.'_/', $key)) {
                    $choice_id = explode('_', $key)[3];
                    $choices = new QuestionChoices();
                    $choices->id = $choice_id;
                    $choices->choice = $value;
                    $choices->image_url = $action_data['ActionForm']['choice_image_url_'.$params['question']->id.'_'.$choice_id];
                    $choices->choice_num = $choice_num;
                    $params['choices'][] = $choices;
                    $choice_num += 1;
                }
            }

        } elseif($params['question']->id < 0) { // 新規作成時
            // 新規作成時のデフォルト値
            $params['relation']->requirement_flg = CpQuestionnaireService::QUESTION_REQUIRED;
            $params['requirement']->random_order_flg = CpQuestionnaireService::NOT_RANDOM_ORDER;
            $params['requirement']->multi_answer_flg = CpQuestionnaireService::SINGLE_ANSWER;
            if ($params['question']->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE) {
                $params['requirement']->use_other_choice_flg = CpQuestionnaireService::NOT_USE_OTHER_CHOICE;
            }
        } else {
            $cp_questionnaire_service = new CpQuestionnaireService();
            $params['relation'] = $cp_questionnaire_service->getRelationByQuestionnaireActionIdAndQuestionId($action_data['cp_questionnaire_action_id'], $params['question']->id);
            $params['requirement'] = $cp_questionnaire_service->getRequirementByQuestionId($params['question']->id);
            $params['choices'] = new QuestionChoices();
            $params['choices'] = $cp_questionnaire_service->getChoicesByQuestionId($params['question']->id);
        }
        return $params;
    }

}