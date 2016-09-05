<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');

class api_execute_questionnaire_action extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_entry_action';
    protected $Form = array(
        'package' => 'message',
        'action' => 'thread/{cp_action_id}',
    );

    private $user_answers;
    /** @var CpQuestionnaireService $cp_questionnaire_service */
    protected $cp_questionnaire_service;

    public function doThisFirst() {
        $service_factory = new aafwServiceFactory();
        $this->cp_questionnaire_service = $service_factory->create('CpQuestionnaireService');
    }

    public function validate() {

        foreach($this->POST as $key=>$value) {
            if(preg_match('/^single_answer|^multi_answer|^free_answer/', $key)) {
                $this->user_answers[$key] = $value;
            }
        }

        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if(!$validator->isValid()) {
            $json_data = $this->createAjaxResponse("ng", array(), $this->errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        $this->Validator = new aafwValidator();

        $cp_questionnaire_action = $this->cp_questionnaire_service->getCpQuestionnaireAction($this->cp_action_id);
        $questionnaires_questions_relations = $this->cp_questionnaire_service->getRelationsByQuestionnaireActionId($cp_questionnaire_action->id);

        // 必須設問で回答があるかチェック
        foreach($questionnaires_questions_relations as $relation) {
            $question = $this->cp_questionnaire_service->getQuestionById($relation->question_id);
            if($relation->requirement_flg) {
                if(QuestionTypeService::isChoiceQuestion($question->type_id)) {

                    $choice_requirement = $this->cp_questionnaire_service->getRequirementByQuestionId($question->id);
                    if($choice_requirement->multi_answer_flg == CpQuestionnaireService::SINGLE_ANSWER) {
                        if(!$this->user_answers['single_answer/' . $question->id]) {
                            $errors['question/' . $question->id] = '必ず回答してください。';
                            continue;
                        }
                    } else {
                        if(!preg_grep('/^multi_answer\/' . $question->id . '\//', array_keys($this->user_answers))) {
                            $errors['question/' . $question->id] = '必ず回答してください。';
                            continue;
                        }
                    }
                } else {
                    if($this->user_answers['free_answer/' . $question->id] === '') {
                        $errors['question/' . $question->id] = '必ず回答してください。';
                        continue;
                    }
                }
            }
        }

        foreach($this->user_answers as $key=>$value) {
            $question_type = explode('/', $key)[0];
            $question_id = explode('/', $key)[1];
            if($question_type == 'single_answer_othertext' || $question_type == 'multi_answer_othertext') {
                //文字数のカウント
                if(mb_strlen($value, 'UTF-8') > 255) {
                    $errors['question/' . $question_id] = '回答は255字以内で入力してください。';
                    continue;
                }
            }
            if($question_type == 'free_answer') {
                //文字数のカウント
                if(mb_strlen($value, 'UTF-8') > 2048) {
                    $errors['question/' . $question_id] = '回答は2048字以内で入力してください。';
                    continue;
                }
            }
            if($question_type == 'single_answer_othertext' || $question_type == 'multi_answer_othertext' || $question_type == 'free_answer') {
                //文字列チェック
                if(!is_string($value)) {
                    $errors['question/' . $question_id] = '回答は文字列で入力してください。';
                    continue;
                }
            }
            // その他を選択しているが、内容の記載がない
            if($question_type == 'single_answer_othertext') {
                $other_choice_id = $this->getOtherChoiceId($question_id);
                if($this->user_answers['single_answer/' . $question_id] == $other_choice_id && $value === '') {
                    $errors['question/' . $question_id] = 'その他を選択した場合、内容を入力してください。';
                    continue;
                }
            }

            if($question_type == 'multi_answer_othertext') {
                $other_choice_id = $this->getOtherChoiceId($question_id);
                if($this->user_answers['multi_answer/' . $question_id . '/' . $other_choice_id] && $value === '') {
                    $errors['question/' . $question_id] = 'その他を選択した場合、内容を入力してください。';
                    continue;
                }
            }
        }

        if($errors) {
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function saveData() {

        $brands_users_relation = $this->getBrandsUsersRelation();

        // 回答の保存
        foreach($this->user_answers as $key=>$value) {
            list($question_type, $question_id, $choice_id) = explode('/', $key);
            $other_choice = $this->cp_questionnaire_service->getOtherChoice($question_id);

            $questionnaires_questions_relation = $this->cp_questionnaire_service->getRelationByQuestionnaireActionIdAndQuestionId($this->cp_questionnaire_action_id, $question_id);

            if($question_type == 'single_answer') {
                if($value) {
                    // その他があればその他の回答を保存
                    if($other_choice && $value == $other_choice->id && $this->user_answers['single_answer_othertext/' . $question_id]) {
                        $other_text_answer = $this->user_answers['single_answer_othertext/' . $question_id];
                    } else {
                        $other_text_answer = null;
                    }
                    $this->cp_questionnaire_service->setQuestionChoiceAnswer($questionnaires_questions_relation->id, $brands_users_relation->id, $question_id, $value, $other_text_answer);
                }
            } elseif($question_type == 'multi_answer') {
                if($value) {
                    // その他があればその他の回答を保存
                    if($other_choice && $value == $other_choice->id && $this->user_answers['multi_answer_othertext/' . $question_id . '/' . $choice_id]) {
                        $other_text_answer = $this->user_answers['multi_answer_othertext/' . $question_id . '/' . $choice_id];
                    } else {
                        $other_text_answer = null;
                    }
                    $this->cp_questionnaire_service->setQuestionChoiceAnswer($questionnaires_questions_relation->id, $brands_users_relation->id, $question_id, $choice_id, $other_text_answer);
                }
            } elseif($question_type == 'free_answer') {
                if($value) {
                    $this->cp_questionnaire_service->setQuestionFreeAnswer($questionnaires_questions_relation->id, $brands_users_relation->id, $question_id, $value);
                }
            }
        }
    }

    function saveExtraData($next_action_status) {
        // Auto Update questionnaire_user_answer
        $cp_questionnaire_action = $this->cp_questionnaire_service->getCpQuestionnaireAction($this->cp_action_id);

        if ($cp_questionnaire_action->panel_hidden_flg == CpQuestionnaireAction::PANEL_TYPE_AVAILABLE) {
            if ($next_action_status->id) {
                $brands_users_relation = $this->getBrandsUsersRelation();

                $user_answer_service = $this->getService('QuestionnaireUserAnswerService');

                $user_answer = $user_answer_service->createEmptyObject();
                $user_answer->cp_action_id = $this->cp_action_id;
                $user_answer->brands_users_relation_id = $brands_users_relation->id;
                $user_answer->finished_answer_id = $next_action_status->id;
                $user_answer->approval_status = QuestionnaireUserAnswer::APPROVAL_STATUS_APPROVE;
                $user_answer->finished_answer_at = date('Y-m-d H:i:s');

                $user_answer_service->saveQuestionnaireUserAnswer($user_answer);
            }
        }
    }

    private function getOtherChoiceId($question_id) {
        $other_choice = $this->cp_questionnaire_service->getOtherChoice($question_id);
        return $other_choice->id;
    }
}
