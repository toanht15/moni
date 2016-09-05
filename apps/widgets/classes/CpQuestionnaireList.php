<?php
AAFW::import('jp.aainc.widgets.base.AdminCpListBase');
AAFW::import('jp.aainc.classes.service.QuestionTypeService');

class CpQuestionnaireList extends AdminCpListBase {
    const PAGE_LIMIT = 10;

    public function doService($params = array()) {
        $params['page'] = $params['page'] ?: 1;
        $params['page_limit'] = $params['page_limit'] ?: self::PAGE_LIMIT;

        /** @var ContentApiCodeService $api_code_service */
        $api_code_service = $this->getService('ContentApiCodeService');
        $api_code = $api_code_service->getApiCodeByCpActionId($params['action_id']);

        $params['api_url'] = $api_code ? $api_code_service->getApiUrl($api_code->code, $this->getCurCpActionType()) : '';
        $export_question_ids = json_decode($api_code->extra_data);

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $params['questionnaire_actions'] = $cp_flow_service->getCpActionsByCpIdAndActionType($params['cp_id'], $this->getCurCpActionType());

        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = $this->getService('CpQuestionnaireService');
        $params['cur_questionnaire_action'] = $cp_questionnaire_service->getCpQuestionnaireAction($params['action_id']);

        /** @var QuestionnaireUserAnswerService $questionnaire_user_answer_service */
        $questionnaire_user_answer_service = $this->getService('QuestionnaireUserAnswerService');
        $params['approved_count'] = $questionnaire_user_answer_service->countQuestionnaireAnswerByCpActionId($params['action_id'], QuestionnaireUserAnswer::APPROVAL_STATUS_APPROVE);

        $db = aafwDataBuilder::newBuilder();
        $qq_info = $db->getQuestionnaireQuestionInfo(array('cp_questionnaire_action_id' => $params['cur_questionnaire_action']->id), null, null, false);
        $question_list = array();

        foreach ($qq_info as $qq) {
            if (!$params['qqs'][$qq['q_no']]['data']) {
                $params['qqs'][$qq['q_no']]['data'] = array(
                    'id' => $qq['q_id'],
                    'type' => $qq['type_id'],
                    'type_text' => QuestionTypeService::getQuestionTypeText($qq['type_id']),
                    'question' => $qq['question'],
                    'use_other_choice_flg' => $qq['use_other_choice_flg'],
                    'multi_answer_flg' => $qq['multi_answer_flg']
                );

                if ($params['target_all'] || in_array($qq['q_id'], $params['targeted_question_ids'])) {
                    $params['qqs'][$qq['q_no']]['data']['targeted'] = true;
                }

                if (in_array($qq['q_id'], $export_question_ids)) {
                    $params['qqs'][$qq['q_no']]['data']['exporting'] = true;
                }

                $question_list[$qq['q_id']] = $qq['q_no'];
            }

            $params['qqs'][$qq['q_no']]['choice_data'][$qq['qc_id']] = array(
                'choice_no' => $qq['choice_num'],
                'choice' => $qq['choice'],
                'image_url' => $qq['image_url']
            );
        }

        // Return if target_question is not valid
        if ($params['target_all']) {
            $params['targeted_question_ids'] = array_keys($question_list);
        } elseif (empty($params['targeted_question_ids'])) {
            return $params;
        }

        $order = $this->getUserDataOrder($params);
        $search_params = $this->getSearchParams($params);

        $pager = array(
            'page' =>  $params['page'],
            'count' => $params['page_limit'],
        );

        // TODO check if q_choices is del or not
        $search_conditions = array(
            'brand_id' => $params['brand_id'],
            'cp_action_id' => $params['action_id']
        );
        $user_list = $cp_questionnaire_service->getUserAnswerList($search_conditions, $pager, $search_params, $order);

        $params['total_count'] = $user_list['pager']['count'];
        $total_page = floor($params['total_count'] / $params['page_limit']) + ($params['total_count'] % $params['page_limit'] > 0);
        $params['page'] = Util::getCorrectPaging($params['page'], $total_page);

        // Return if user is not valid
        if (empty($user_list['list'])) {
            return $params;
        }

        $bur_list = array();
        foreach ($user_list['list'] as $user) {
            $bur_list[] = $user['bur_id'];
            $params['qas'][$user['bur_id']]['user_info'] = array(
                'bur_no' => $user['no'],
                'name' => $user['name'],
                'approval_status' => $user['approval_status'] ?: QuestionnaireUserAnswer::APPROVAL_STATUS_UNAPPROVED,
                'profile_image_url' => $user['profile_image_url'],
                'created_at' => date('m/d', strtotime($user['created_at'])),
            );
        }

        $choice_answers = $cp_questionnaire_service->getChoiceAnswersByBurIds($bur_list, $params['targeted_question_ids']);
        foreach ($choice_answers as $choice_answer) {
            $cur_q_no = $question_list[$choice_answer->question_id];

            $choice_data = $params['qqs'][$cur_q_no]['choice_data'][$choice_answer->choice_id];
            $answer_data = array(
                'choice' => $choice_data['choice'],
                'image_url' => $choice_data['image_url']
            );

            if (!Util::isNullOrEmpty($choice_answer->answer_text)) {
                $answer_data['other_text'] = $choice_answer->answer_text;
            }

            $params['qas'][$choice_answer->brands_users_relation_id]['answer_data'][$cur_q_no][$choice_data['choice_no']] = $answer_data;
        }

        $free_answers = $cp_questionnaire_service->getFreeAnswersByBurIds($bur_list, $params['targeted_question_ids']);
        foreach ($free_answers as $free_answer) {
            $cur_q_no = $question_list[$free_answer->question_id];
            $params['qas'][$free_answer->brands_users_relation_id]['answer_data'][$cur_q_no]['answer_text'] = $free_answer->answer_text;
        }

        return $params;
    }

    public function getSearchParams($params) {
        $search_params = array();

        /**
         * Approval value {'1' => '承認', '2' => '非承認', '3' => '未承認'}
         */
        if ($params['approval_status'] && $params['approval_status'] != 1) {
            $search_params['approval_status'] = $params['approval_status'] - 1;
        }

        return $search_params;
    }

    public function getCurCpActionType() {
        return CpAction::TYPE_QUESTIONNAIRE;
    }

    public function doSubService($params) {}
}