<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');

class api_get_question extends BrandcoGETActionBase {
    protected $ContainerName = 'save_action_questionnaire';

    public $NeedOption = array();
    protected $AllowContent = array('JSON');

    public function validate() {
        return true;
    }

    function doAction() {

        if (!$this->cp_questionnaire_action_id && $this->question_type == CpQuestionnaireService::TYPE_PROFILE_QUESTION) {
            $this->getProfileQuestion();
        } else {
            $this->getCpQuestion();
        }
        return 'dummy.php';
    }

    private function getCpQuestion() {
        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = $this->createService('CpQuestionnaireService');

        // 参加者一覧の「メッセージ作成」の方でエラーが発生した場合は、保存ができないのでセッションから取得
        if($this->SESSION['cpQuestionnaireError']) {
            $questions = array();
            if ($this->SESSION['cpQuestionnaireError']->getError('is_fan_list_page')) {
                //is_fan_list_pageなら、エラーがあるときにデータを保存しないので、全てのActionFormからデータを取って表示します。
                foreach($this->Data['ActionForm'] as $key=>$value) {
                    if(preg_match('/^question_id_/', $key)) {
                        $question_id = explode('_', $key)[2];
                        $question_data = new QuestionnaireQuestions();
                        $question_data->id = $question_id;
                        $question_data->type_id = $this->Data['ActionForm']['type_'.$question_id];
                        $question_data->question = $value;
                        $questions[] = $question_data;
                    }
                }
            } else {
                //edit actionからのとき、エラーがあっても新しいデータを作成するのでアンケートのテキストのみActionFormから取って表示します。
                $questions = $cp_questionnaire_service->getQuestionsByQuestionnaireActionId($this->cp_questionnaire_action_id);
                foreach ($questions as $key => $question) {
                    if (array_key_exists('question_id_' . $question->id, $this->Data['ActionForm'])) {
                        $questions[$key]->question = $this->Data['ActionForm']['question_id_' . $question->id];
                    }
                }
            }
        } else {
            $questions = $cp_questionnaire_service->getQuestionsByQuestionnaireActionId($this->cp_questionnaire_action_id);
        }
        $parser = new PHPParser();
        $this->Data['cp_questionnaire_action_id'] = $this->cp_questionnaire_action_id;
        $this->Data['question_list'] = $questions;
        $this->Data['action_status'] = $this->status == CpAction::STATUS_FIX ? CpAction::STATUS_FIX : CpAction::STATUS_DRAFT;
        $this->Data['cp_questionnaire_errors'] = $this->SESSION['cpQuestionnaireError'];
        $this->setSession('cpQuestionnaireError', null);

        $html = $parser->parseTemplate(
            'CpQuestionnaireList.php',
            $this->Data
        );

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);

        // セッションを削除
        $this->resetActionContainerByKey('Errors');
        return 'dummy.php';
    }

    private function getProfileQuestion() {
        AAFW::import('jp.aainc.classes.services.QuestionTypeService');

        $data = array();
        $data['question_list'] = array();
        $data['choices'] = array();

        if($this->SESSION['UserSettingError']) {
            $data['UserSettingError'] = $this->SESSION['UserSettingError'];
            foreach($this->SESSION['UserSettingValidateError'] as $key=>$value) {
                if(preg_match('/^question\//', $key)) {
                    list($question, $question_id) = explode('/', $key);
                    $data['question_list'][] = $question_id;

                } else if(preg_match('/^choice\//', $key)) {
                    list($choice, $question_id, $choice_id) = explode('/', $key);
                    if (!$data['choices'][$question_id]) {
                        $data['choices'][$question_id] = array();
                    }
                    $data['choices'][$question_id][] = $choice_id;
                }
                $data[$key] = $value;
            }
            $this->SESSION['UserSettingError'] = null;
            $this->SESSION['UserSettingValidateError'] = null;
        } else {
            if ($this->is_new_question) {
                $id = 'new_'.$this->question_num;
                $data['question_list'][] = $id;
                $data['is_requirement/'.$id] = 1;
                $data['choices'][$id][] = 'new_00';
                $data['choices'][$id][] = 'new_01';

            } else {
                /** @var CpQuestionnaireService $questionnaire_service */
                $questionnaire_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
                $existed_profile_questions = $questionnaire_service->getProfileQuestionsByBrandId($this->brand->id);

                foreach ($existed_profile_questions as $question) {
                    $question_relation = $questionnaire_service->getRelationByProfileQuestionId($question->id);
                    $data['question_list'][] = $question->id;
                    $data['question/'.$question->id] = $question->question;
                    $data['question_type/'.$question->id] = $question->type_id;
                    $data['is_requirement/'.$question->id] = $question_relation->requirement_flg;
                    $data['is_use/'.$question->id] = $question_relation->public;
                    if ($question->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE || $question->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) {
                        $question_requirement = $questionnaire_service->getRequirementByQuestionId($question->id);
                        $data['is_multi_answer/'.$question->id] = $question_requirement->multi_answer_flg;
                        $data['is_random_choice/'.$question->id] = $question_requirement->random_order_flg;
                        $data['is_use_other/'.$question->id] = $question_requirement->use_other_choice_flg;

                        $choices = $questionnaire_service->getChoicesByQuestionId($question->id);
                        foreach ($choices as $choice) {
                            $data['choice/'.$question->id.'/'.$choice->id] = $choice->choice;
                        }
                    }

                    $choices = $questionnaire_service->getChoicesByQuestionId($question->id);
                    $data['choices'][$question->id] = array();
                    foreach ($choices as $choice) {
                        if ($choice->other_choice_flg) {
                            continue;
                        }
                        $data['choices'][$question->id][] = $choice->id;
                    }
                }
                //最初アンケート生成
                if (count($data['question_list']) == 0) {
                    $data['question_list'][] = 'new_00';
                    $data['is_requirement/new_00'] = 1;
                    $data['choices']['new_00'][] = 'new_00';
                    $data['choices']['new_00'][] = 'new_01';

                    $data['question_list'][] = 'new_01';
                    $data['is_requirement/new_01'] = 1;
                    $data['choices']['new_01'][] = 'new_00';
                    $data['choices']['new_01'][] = 'new_01';
                }
            }
        }
        $parser = new PHPParser();

        $html = $parser->parseTemplate(
            'ProfileQuestionTemplate.php',
            $data
        );

        $json_data = $this->createAjaxResponse("ok", array('question_count' => count($data['question_list'])), array(), $html);
        $this->assign('json_data', $json_data);
    }
}
