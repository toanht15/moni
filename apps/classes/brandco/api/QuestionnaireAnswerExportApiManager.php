<?php
AAFW::import('jp.aainc.classes.brandco.api.base.ContentExportApiManagerBase');
AAFW::import('jp.aainc.classes.service.QuestionTypeService');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class QuestionnaireAnswerExportApiManager extends ContentExportApiManagerBase {

    public function doSubProgress() {

        $db = new aafwDataBuilder();

        $param = array(
            'code' => $this->code,
            'max_id' => $this->max_id ? $this->max_id : null,
            'cp_action_type' => CpAction::TYPE_QUESTIONNAIRE
        );

        $pager = array(
            'page' => self::DEFAULT_PAGE,
            'count' => $this->limit + 1         // $questionnaire_answer_count = $page_limit + $next_min_user
        );

        $order = array(
            'name' => 'finished_answer_id',
            'direction' => 'desc'
        );

        $result = $db->getQuestionnaireAnswerByContentApiCodes($param, $order, $pager, true, 'QuestionnaireUserAnswer');
        $user_answers = $result['list'];

        if (!$user_answers) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'アンケートデータが存在しません'));
            return $json_data;
        }

        /** @var ContentApiCodeService $api_code_service */
        $api_code_service = $this->service_factory->create('ContentApiCodeService');
        $content_api_code = $api_code_service->getApiCodeByCode($this->code);

        if (!json_decode($content_api_code->extra_data)) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'アンケートデータが存在しません'));
            return $json_data;
        }

        // API Pagination
        $pagination = array();
        if ($result['pager']['count'] >= $this->limit + 1) {
            // If next_min_user is available pop it from $user_answers list
            $last_user_answer = array_pop($user_answers);

            $pagination = array(
                'next_id' => $last_user_answer->finished_answer_id,
                'next_url'    => $api_code_service->getApiUrl($this->code, CpAction::TYPE_QUESTIONNAIRE, $last_user_answer->finished_answer_id, $this->limit)
            );
        }

        $raw_data = array(
            'content_api_code' => $content_api_code,
            'user_answers' => $user_answers
        );

        $response_data = $this->getApiExportData($raw_data, $this->getBrand());
        $json_data = $this->createResponseData('ok', $response_data, array(), $pagination);
        return $json_data;
    }

    /**
     * @param $raw_data
     * @param null $brand
     * @return array
     */
    public function getApiExportData($raw_data, $brand = null) {
        $content_api_code = $raw_data['content_api_code'];
        $user_answers = $raw_data['user_answers'];
        $export_question_id = json_decode($content_api_code->extra_data);

        // Get Question's data

        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = $this->service_factory->create('CpQuestionnaireService');
        $params['cur_questionnaire_action'] = $cp_questionnaire_service->getCpQuestionnaireAction($content_api_code->cp_action_id);

        $db = aafwDataBuilder::newBuilder();
        $qq_info_condition = array(
            'cp_questionnaire_action_id' => $params['cur_questionnaire_action']->id,
            'question_ids' => $export_question_id,
        );
        $qq_info = $db->getQuestionnaireQuestionInfo($qq_info_condition, null, null, false);

        $question_data = array();
        $question_list = array();
        $choice_data = array();

        foreach ($qq_info as $qq) {
            if (!$question_data[$qq['q_no']]) {
                $question_list[$qq['q_id']] = $qq['q_no'];
                $question_data[$qq['q_no']] = array(
                    'q_text' => $qq['question'],
                    'q_type_id' => $qq['type_id'],
                    'q_type' => QuestionTypeService::getQuestionTypeText($qq['type_id'])
                );
            }

            if ($qq['type_id'] != QuestionTypeService::FREE_ANSWER_TYPE) {
                $cur_data = array(
                    'q_choice_id' => $qq['choice_num'],
                    'q_choice_text' => $qq['choice']
                );

                if ($qq['type_id'] == QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE) {
                    $cur_data['q_choice_img_url'] = $qq['image_url'];
                }

                $choice_data[$qq['q_no']][$qq['qc_id']] = $cur_data;
            }
        }

        foreach ($choice_data as $q_no => $q_choices) {
            ksort($q_choices);
            $question_data[$q_no]['q_choices'] = array_values($q_choices);
        }

        // Get Answers

        $answer_data = array();
        foreach ($user_answers as $user_answer) {
            $answer_data[$user_answer->brands_users_relation_id] = array(
                'id' => $user_answer->id,
                'bur_id' => $user_answer->brands_users_relation_id,
                'answered_at' => $user_answer->finished_answer_at
            );
        }

        $bur_list = array_keys($answer_data);

        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = $this->service_factory->create('CpQuestionnaireService');
        $choice_answers = $cp_questionnaire_service->getChoiceAnswersByBurIds($bur_list, $export_question_id);

        foreach ($choice_answers as $choice_answer) {
            $cur_q_no = $question_list[$choice_answer->question_id];
            $answer_text = $choice_data[$cur_q_no][$choice_answer->choice_id]['q_choice_text'];

            if (!Util::isNullOrEmpty($choice_answer->answer_text)) {
                $answer_text .= '（' . $choice_answer->answer_text . '）';
            }

            $cur_data = array('answer_text' => $answer_text);
            if ($question_data[$cur_q_no]['q_type_id'] == QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE) {
                $cur_data['image_url'] = $choice_data[$cur_q_no][$choice_answer->choice_id]['q_choice_img_url'];;
            }

            $answer_data[$choice_answer->brands_users_relation_id]['q_answers'][$cur_q_no][] = $cur_data;
        }

        $free_answers = $cp_questionnaire_service->getFreeAnswersByBurIds($bur_list, $export_question_id);
        foreach ($free_answers as $free_answer) {
            $cur_q_no = $question_list[$free_answer->question_id];

            $answer_data[$free_answer->brands_users_relation_id]['q_answers'][$cur_q_no][] = array(
                'answer_text' => $free_answer->answer_text
            );
        }

        foreach ($answer_data as $bur_id => $answer) {
            ksort($answer['q_answers']);
            $data = array_values($answer['q_answers']);
            $answer_data[$bur_id]['q_answers'] = $data;
        }

        return array(
            'total_answers' => $this->getTotalAnswers(),
            'questions' => array_values($question_data),
            'answers' => array_values($answer_data)
        );
    }

    /**
     * @return mixed
     */
    public function getTotalAnswers(){
        $db = aafwDataBuilder::newBuilder();

        $param = array(
            'code' => $this->code,
            'cp_action_type' => CpAction::TYPE_QUESTIONNAIRE
        );

        $result = $db->getCountQuestionnaireAnswerByContentApiCodes($param);

        return $result[0]['COUNT(*)'];
    }
}