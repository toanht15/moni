<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.QuestionTypeService');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');

class save_profile_question extends BrandcoPOSTActionBase {
    protected $ContainerName = 'user_settings';
    protected $Form = array(
        'package' => 'admin-settings',
        'action' => 'user_settings_form',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    private $questions;
    private $choices;
    private $not_valid_question;

    protected $ValidatorDefinition = array(
        'title' => array(
            'type' => 'str',
            'length' => 30
        )
    );

    public function doThisFirst() {
        $this->questions = array();
        $this->choices = array();

        /** @var CpQuestionnaireService $questionnaire_service */
        $questionnaire_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        $this->Form['action'] = 'user_settings_form?mid=failed&f=pq';

        // POSTしてきた設問、選択肢を配列に格納
        foreach($this->POST as $key => $value) {
            if(preg_match('/^choice\//', $key)) {
                // 選択肢のinputのname構成は、choice/設問ID/選択肢IDとなるので、choice_info[1]は設問ID、choice_info[2]は選択肢IDとなる
                $choice_info = explode('/', $key);

                if (!$choice_info[0] || !$choice_info[1] || !$choice_info[2]) {
                    $this->not_valid_question = true;
                    continue;
                }

                $question = $questionnaire_service->getQuestionById($choice_info[1]);
                $type = $question ? $question->type_id : $this->POST['question_type/'.$choice_info[1]];

                if($type == QuestionTypeService::CHOICE_ANSWER_TYPE) {
                    $this->choices[$choice_info[1]][$choice_info[2]] = $value;
                    $this->ValidatorDefinition[$key] = array(
                        'required' => true,
                        'type' => 'str',
                        'length' => 1024
                    );
                } elseif($type == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE && is_numeric($choice_info[1])) {
                    $this->choices[$choice_info[1]][$choice_info[2]] = $value;
                    $this->ValidatorDefinition[$key] = array(
                        'required' => true,
                        'type' => 'str',
                        'length' => 12
                    );
                }
            } else if (preg_match('/^textareaChoice\//', $key)) {
                // プルダウンのinputのname構成は、textareaChoice/設問IDとなるので、text_choice_info[1]は設問IDとなる
                $textarea_choice_info = explode('/', $key);

                if (!$textarea_choice_info[0] || !$textarea_choice_info[1]) {
                    $this->not_valid_question = true;
                    continue;
                }

                $question = $questionnaire_service->getQuestionById($textarea_choice_info[1]);
                $type = $question ? $question->type_id : $this->POST['question_type/'.$textarea_choice_info[1]];

                if($type == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) {
                    $textarea_choice = $value;
                    $choice_info = Util::cutStringByLineBreak($textarea_choice);

                    $prov_choice_id = 1; //仮置きのchoice_id
                    foreach($choice_info as $choice) {
                        if($choice !== '') {
                            $this->choices[$textarea_choice_info[1]]['new_'.$prov_choice_id] = $choice;
                            $prov_choice_id += 1;
                        }
                    }
                    $this->ValidatorDefinition[$key] = array(
                        'required' => true,
                        'type' => 'str'
                    );
                }
            } else if (preg_match('/^question\//', $key)) {
                // 設問のinputのname構成は、question/設問IDとなるので、question_info[1]は設問IDとなる
                $question_info = explode('/', $key);

                if (!$question_info[0] || !$question_info[1]) {
                    $this->not_valid_question = true;
                }
                $this->questions[$question_info[1]] = $value;
                $this->ValidatorDefinition[$key] = array(
                    'required' => true,
                    'type' => 'str',
                    'length' => 1024
                );
            }
        }
    }

    public function validate () {

        if ($this->not_valid_question) {
            return false;
        }
        /** @var CpQuestionnaireService $questionnaire_service */
        $questionnaire_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        foreach ($this->questions as $question_id => $value) {
            if (is_numeric($question_id)) {
                $question = $questionnaire_service->getQuestionById($question_id);
                $question_relation = $questionnaire_service->getRelationByProfileQuestionId($question_id);
                if (!$question || $question_relation->brand_id != $this->brand->id) {
                    return '404';
                }
                $question_type = $question->type_id;
            } else {
                $question_type = $this->POST['question_type/'.$question_id];
            }

            if ($question_type == QuestionTypeService::CHOICE_ANSWER_TYPE) {
                if (count($this->choices[$question_id]) < 1) {
                    $this->Validator->setError('question/' . $question_id, 'NO_CHOICE');
                }
                foreach ($this->choices[$question_id] as $choice_id => $value) {
                    if (!$this->isNumeric($choice_id)) {
                        continue;
                    }
                    $choice = $questionnaire_service->getChoiceById($choice_id);
                    if (!$choice || $choice->question_id != $question_id) {
                        return false;
                    }
                }
            }

            if ($question_type == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) {
                if (is_numeric($question_id)) {
                    if (count($this->choices[$question_id]) < 1) {
                        $this->Validator->setError('question/' . $question_id, 'NO_CHOICE');
                    }
                    foreach ($this->choices[$question_id] as $choice_id => $value) {
                        if (!$this->isNumeric($choice_id)) {
                            continue;
                        }
                        $choice = $questionnaire_service->getChoiceById($choice_id);
                        if (!$choice || $choice->question_id != $question_id) {
                            return false;
                        }
                    }
                } else {
                    foreach ($this->choices[$question_id] as $choice) {
                        if (mb_strlen($choice, 'UTF-8') > 12) {
                            $this->Validator->setError('textareaChoice/' . $question_id, 'INPUT_WITHIN_12_PER_LINE');
                        }
                    }
                }
            }

            if($question_type == QuestionTypeService::FREE_ANSWER_TYPE) {
                /** @var QuestionNgWordService $questionNgWordService */
                $questionNgWordService = $this->createService('QuestionNgWordService');
                if($questionNgWordService->isNgQuestion($value,$this->brand->id)) {
                    $this->Validator->setError('question/' . $question_id, 'NG_QUESTION');
                }
            }
        }

        return $this->Validator->isValid();
    }

    function doAction() {

        /** @var CpQuestionnaireService $questionnaire_service */
        $questionnaire_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        try {
            $questionnaire_service->questionnaire_question->begin();

            $this->deleteQuestionsAndChoices();

            if ($this->POST['question_order']) {
                $question_order_array = explode(',', $this->POST['question_order']);
            } else {
                $question_order_array = array();
            }

            $question_base_order = 1;
            foreach ($this->questions as $question_id => $question_text) {
                $type_id = $this->POST['question_type/'.$question_id];
                $question_relation = '';

                list ($type_id, $question) = $this->setQuestion($question_id, $type_id, $question_text);

                //set order of question
                $question_order = array_search($question_id, $question_order_array);
                if (is_numeric($question_order)) {
                    $question_order += 1;
                } else {
                    $question_order = $question_relation->number ? $question_relation->number : $question_base_order;
                }
                $question_base_order++;
                $public_flg = $this->POST['is_use/'.$question_id] ? 1 : 0;
                $questionnaire_service->setProfileQuestionnairesQuestionsRelation($this->brand->id, $question->id, $this->POST['is_requirement/'.$question_id], $question_order, $public_flg);

                if ($type_id == QuestionTypeService::CHOICE_ANSWER_TYPE || $type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) {
                    $use_other_choice_flg = $type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE ? CpQuestionnaireService::NOT_USE_OTHER_CHOICE : $this->POST['is_use_other/' . $question_id];
                    $random_order_flg = $this->POST['is_random_choice/' . $question_id];
                    $multi_answer_flg = $type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE ? CpQuestionnaireService::SINGLE_ANSWER : $this->POST['is_multi_answer/' . $question_id];
                    $questionnaire_service->setRequirement($question->id, $use_other_choice_flg, $random_order_flg, $multi_answer_flg);

                    if ($this->POST['choice_order_'.$question_id]) {
                        $choice_order_array = explode(',', $this->POST['choice_order_'.$question_id]);
                    } else {
                        $choice_order_array = array();
                    }
                    $choice_base_order = 1;
                    foreach ($this->choices[$question_id] as $choice_id => $choice_text) {
                        $choice_id = $this->isNumeric($choice_id) ? $choice_id : -1;
                        $choice = $questionnaire_service->getChoiceById($choice_id);

                        //set order of choice
                        $choice_order = array_search($choice_id, $choice_order_array);
                        if (is_numeric($choice_order)) {
                            $choice_order += 1;
                        } else {
                            $choice_order = $choice->choice_num ? $choice->choice_num : $choice_base_order;
                        }
                        $choice_base_order++;
                        $questionnaire_service->setChoices($question->id, $choice_text, $choice_id, $choice_order);
                    }

                    if ($type_id == QuestionTypeService::CHOICE_ANSWER_TYPE) {
                        if ($use_other_choice_flg) {
                            $questionnaire_service->setOtherChoice($question->id);
                        } else {
                            $questionnaire_service->deleteOtherChoice($question->id);
                        }
                    }
                }
            }

            $questionnaire_service->questionnaire_question->commit();
        } catch (Exception $e) {
            $questionnaire_service->questionnaire_question->rollback();
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        }

        return 'redirect: '.Util::rewriteUrl('admin-settings', 'user_settings_form', array(), array('mid'=>'updated'));
    }

    public function deleteQuestionsAndChoices () {

        /** @var CpQuestionnaireService $questionnaire_service */
        $questionnaire_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        /** @var CpEntryProfileQuestionnaireService $cp_entry_profile_questionnaire_service */
        $cp_entry_profile_questionnaire_service = $this->createService('CpEntryProfileQuestionnaireService');

        $existing_profile_questions = $questionnaire_service->getProfileQuestionsByBrandId($this->brand->id);

        foreach ($existing_profile_questions as $question) {
            if (array_key_exists($question->id, $this->questions)) {
                if ($question->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE || $question->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) {
                    $choices = $questionnaire_service->getChoicesByQuestionId($question->id);
                    foreach ($choices as $choice) {
                        if (!$choice->other_choice_flg && !array_key_exists($choice->id, $this->choices[$question->id])) {
                            $questionnaire_service->deleteChoice($choice);
                        }
                    }
                }
            } else {
                $cp_entry_profile_questionnaire_service->deleteEntryQuestionnairesByProfileQuestionnaireId($question->id);
                if ($question->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE || $question->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) {
                    $choices = $questionnaire_service->getChoicesByQuestionId($question->id);
                    foreach ($choices as $choice) {
                        $questionnaire_service->deleteChoice($choice);
                    }
                    $question_requirement = $questionnaire_service->getRequirementByQuestionId($question->id);
                    $questionnaire_service->deleteQuestionRequirement($question_requirement);
                    $question_relation = $questionnaire_service->getRelationByProfileQuestionId($question->id);
                    $questionnaire_service->deleteQuestionnairesQuestionsRelation($question_relation);
                    $questionnaire_service->deleteQuestion($question);
                } else {

                    $question_relation = $questionnaire_service->getRelationByProfileQuestionId($question->id);
                    $questionnaire_service->deleteQuestionnairesQuestionsRelation($question_relation);
                    $questionnaire_service->deleteQuestion($question);
                }
            }
        }
    }

    public function setQuestion($question_id, $type_id, $question_text) {
        /** @var CpQuestionnaireService $questionnaire_service */
        $questionnaire_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        if ($this->isNumeric($question_id)) {
            $question = $questionnaire_service->getQuestionById($question_id);
            $question = $questionnaire_service->setQuestion($question->type_id, $question_text, $question->id);

            $type_id = $question->type_id;
        } else {
            if (!$type_id) {
                throw new Exception('プロフィールアンケートタイプ'.$question_id.'がない。');
            }
            $question = $questionnaire_service->setQuestion($type_id, $question_text, -1);
        }

        return array($type_id, $question);
    }
}
