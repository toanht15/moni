<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.QuestionnairesQuestionsRelation');
AAFW::import('jp.aainc.classes.entities.QuestionChoiceRequirement');
AAFW::import('jp.aainc.classes.entities.QuestionChoice');
AAFW::import('jp.aainc.classes.services.QuestionTypeService');

class CpQuestionnaireService extends aafwServiceBase {

    const QUESTION_REQUIRED = '1';
    const QUESTION_NOT_REQUIRED = '0';

    const USE_OTHER_CHOICE = '1';
    const NOT_USE_OTHER_CHOICE = '0';

    const RANDOM_ORDER = '1';
    const NOT_RANDOM_ORDER = '0';

    const MULTI_ANSWER = '1';
    const SINGLE_ANSWER = '0';

    const TYPE_CP_QUESTION = '0';
    const TYPE_PROFILE_QUESTION = '1';

    protected $cp_questionnaire_action;
    protected $questionnaires_questions_relation;
    public $questionnaire_question;
    protected $question_requirement;
    protected $question_choice;
    protected $question_choice_answer;
    protected $question_free_answer;
    protected $choice_histories;
    protected $free_histories;
    protected $record_time;

    public function __construct($type = CpQuestionnaireService::TYPE_CP_QUESTION) {

        if ($type === CpQuestionnaireService::TYPE_CP_QUESTION) {
            $this->cp_questionnaire_action = $this->getModel('CpQuestionnaireActions');
            $this->questionnaires_questions_relation = $this->getModel('QuestionnairesQuestionsRelations');
            $this->questionnaire_question = $this->getModel('QuestionnaireQuestions');
            $this->question_requirement = $this->getModel('QuestionChoiceRequirements');
            $this->question_choice = $this->getModel('QuestionChoices');
            $this->question_choice_answer = $this->getModel('QuestionChoiceAnswers');
            $this->question_free_answer = $this->getModel('QuestionFreeAnswers');

        } else if ($type === CpQuestionnaireService::TYPE_PROFILE_QUESTION) {
            $this->questionnaires_questions_relation = $this->getModel('ProfileQuestionnairesQuestionsRelations');
            $this->questionnaire_question = $this->getModel('ProfileQuestionnaireQuestions');
            $this->question_requirement = $this->getModel('ProfileQuestionChoiceRequirements');
            $this->question_choice = $this->getModel('ProfileQuestionChoices');
            $this->question_choice_answer = $this->getModel('ProfileQuestionChoiceAnswers');
            $this->question_free_answer = $this->getModel('ProfileQuestionFreeAnswers');
            $this->choice_histories = $this->getModel('ProfileChoiceAnswerHistories');
            $this->free_histories = $this->getModel('ProfileFreeAnswerHistories');
            $this->record_time = date('Y-m-d H:i:s', time());
        }
    }

    /**
     * CPアクションIDよりアンケートアクションを取得
     * @param $cp_action_id
     */
    public function getCpQuestionnaireAction($cp_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_action_id' => $cp_action_id,
            ),
        );
        return $this->cp_questionnaire_action->findOne($filter);
    }

    /**
     * @param $questionnaire_action_id
     * @param $question_id
     * @param $requirement_flg
     * @param $new_question_num
     * アンケートの質問を保存
     */
    public function setQuestionnairesQuestionsRelation($questionnaire_action_id, $question_id, $requirement_flg, $new_question_num) {

        $questionnaire_question_relation = $this->getRelationByQuestionnaireActionIdAndQuestionId($questionnaire_action_id, $question_id);
        if (!$questionnaire_question_relation->id) {
            $questionnaire_question_relation = $this->createEmptyQuestionnairesQuestionsRelation();
            $questionnaire_question_relation->cp_questionnaire_action_id = $questionnaire_action_id;
            $questionnaire_question_relation->question_id = $question_id;
        }
        $questionnaire_question_relation->requirement_flg = $requirement_flg;
        $questionnaire_question_relation->number = $new_question_num;
        $this->createQuestionnairesQuestionsRelation($questionnaire_question_relation);
    }

    public function setProfileQuestionnairesQuestionsRelation($brand_id, $question_id, $requirement_flg, $new_question_num, $public_flg) {
        $questionnaire_question_relation = $this->getRelationByProfileQuestionId($question_id);
        if (!$questionnaire_question_relation->id) {
            $questionnaire_question_relation = $this->createEmptyQuestionnairesQuestionsRelation();
            $questionnaire_question_relation->brand_id = $brand_id;
            $questionnaire_question_relation->question_id = $question_id;
        }
        $questionnaire_question_relation->requirement_flg = $requirement_flg;
        $questionnaire_question_relation->number = $new_question_num;
        $questionnaire_question_relation->public = $public_flg;
        $this->createQuestionnairesQuestionsRelation($questionnaire_question_relation);
    }

    public function createQuestionnairesQuestionsRelation($questionnaire_question_relation) {
        return $this->questionnaires_questions_relation->save($questionnaire_question_relation);
    }

    public function createEmptyQuestionnairesQuestionsRelation() {
        return $this->questionnaires_questions_relation->createEmptyObject();
    }

    /**
     * アンケートの質問取得
     * @param $cp_questionnaire_action_id
     * @param $question_id
     */
    public function getRelationByQuestionnaireActionIdAndQuestionId($cp_questionnaire_action_id, $question_id) {
        $filter = array(
            'conditions' => array(
                'cp_questionnaire_action_id' => $cp_questionnaire_action_id,
                'question_id' => $question_id,
            ),
        );
        return $this->questionnaires_questions_relation->findOne($filter);
    }

    /**
     * アンケートの質問取得
     * @param $cp_questionnaire_action_id
     * @return $questionnaires_questions_relation
     */
    public function getRelationsByQuestionnaireActionId($cp_questionnaire_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_questionnaire_action_id' => $cp_questionnaire_action_id,
            ),
            'order' => array(
                'name' => 'number',
                'direction' => 'asc',
            ),
        );
        return $this->questionnaires_questions_relation->find($filter);
    }

    /**
     * アンケートの質問取得
     * @param $question_id
     * @return $questionnaires_questions_relation
     */
    public function getRelationsByQuestionId($question_id) {
        $filter = array(
            'conditions' => array(
                'question_id' => $question_id,
            ),
            'order' => array(
                'name' => 'cp_questionnaire_action_id',
                'direction' => 'asc',
            ),

        );
        return $this->questionnaires_questions_relation->find($filter);
    }

    public function getProfileQuestionRelationsById($relation_id) {
        $filter = array(
            'conditions' => array(
                'id' => $relation_id,
            ),
        );
        return $this->questionnaires_questions_relation->findOne($filter);
    }

    public function getRelationByProfileQuestionId($question_id) {
        return $this->questionnaires_questions_relation->findOne(array('question_id' => $question_id));
    }

    public function deleteQuestionnairesQuestionsRelation($questionnaire_question_relation) {
        $this->questionnaires_questions_relation->delete($questionnaire_question_relation);
    }

    /**
     * アンケートの質問取得
     * @param $questionnaire_id
     * @return $questionnaire_question
     */
    public function getQuestionById($question_id) {
        $filter = array(
            'conditions' => array(
                'id' => $question_id,
            ),
        );
        return $this->questionnaire_question->findOne($filter);
    }

    /**
     * アンケートの質問取得
     * @param $question_ids
     * @return mixed
     */
    public function getQuestionByIds($question_ids) {
        $filter = array(
            'id' => $question_ids
        );

        return $this->questionnaire_question->find($filter);
    }

    public function getQuestionMapByIds($question_ids) {
        if ($question_ids === null || count($question_ids)  === 0) {
            return array();
        }
        $filter = array(
            'where' => 'del_flg = 0 AND id IN(' . join(',', $question_ids) . ')'
        );
        $result = $this->questionnaire_question->find($filter);
        $map = array();
        foreach ($result as $row) {
            $map[$row->id] = $row;
        }
        return $map;
    }

    /**
     * @param $type_id
     * @param $question
     * @param $question_id
     * @return $questionnaire_question
     * アンケートの質問を保存
     */
    public function setQuestion($type_id, $question, $question_id) {

        if($question_id < 0) {
            $questionData = $this->createEmptyQuestionData();
            $questionData->type_id = $type_id;
        } else {
            $questionData = $this->getQuestionById($question_id);
        }
        $questionData->question = $question;
        return $this->createQuestionData($questionData);
    }

    public function createQuestionData($questionData) {
        return $this->questionnaire_question->save($questionData);
    }

    public function createEmptyQuestionData() {
        return $this->questionnaire_question->createEmptyObject();
    }

    public function deleteQuestion($question) {
        $this->questionnaire_question->delete($question);
    }

    /**
     * @param $question_id
     * @param $use_other_choice_flg
     * @param $random_order_flg
     * @param $multi_answer_flg
     * @return $question_requirement
     * アンケート要件の保存
     */
    public function setRequirement($question_id, $use_other_choice_flg, $random_order_flg, $multi_answer_flg) {
        $requirement = $this->getRequirementByQuestionId($question_id);
        if (!$requirement->id) {
            $requirement = $this->createEmptyRequirement();
            $requirement->question_id = $question_id;
        }
        $requirement->use_other_choice_flg = $use_other_choice_flg;
        $requirement->random_order_flg = $random_order_flg;
        $requirement->multi_answer_flg = $multi_answer_flg;

        $this->createRequirement($requirement);
    }

    private function createRequirement($requirement) {
        return $this->question_requirement->save($requirement);
    }

    private function createEmptyRequirement() {
        return $this->question_requirement->createEmptyObject();
    }

    /**
     * 要件の取得
     * @param $question_id
     * @return $question_requirement
     */
    public function getRequirementByQuestionId($question_id) {
        $filter = array(
            'conditions' => array(
                'question_id' => $question_id,
            )
        );

        return $this->question_requirement->findOne($filter);
    }

    /**
     * 条件の取得
     * @param $question_ids
     * @return mixed
     */
    public function getRequirementByQuestionIds($question_ids) {
        $filter = array(
            'conditions' => array(
                'question_id' => $question_ids
            )
        );

        return $this->question_requirement->find($filter);
    }

    public function getRequirementMapByQuestionIds($question_ids) {
        if ($question_ids === null || count($question_ids) === 0) {
            return array();
        }
        $filter = array(
            'where' => 'del_flg = 0 AND question_id IN(' . join(',', $question_ids) . ')'
        );
        $result = $this->question_requirement->find($filter);
        $map = array();
        foreach ($result as $row) {
            $map[$row->question_id] = $row;
        }
        return $map;
    }

    /**
     * 選択肢要件を物理削除
     * @param $question_choice_requirement
     */
    public function deleteQuestionRequirement($question_choice_requirement) {
        $this->question_requirement->delete($question_choice_requirement);
    }

    /**
     * @param $question_id
     * @param $choice
     * @param $choice_id
     * @param $new_choice_num
     * @param $other_choice_flg
     * アンケート要件の保存
     */
    public function setChoices($question_id, $choice, $choice_id, $new_choice_num, $image_url=null, $other_choice_flg=null) {
        if($choice_id <= 0 || preg_match('/^a/', $choice_id)) {
            $choiceData = $this->createEmptyChoice();
            $choiceData->question_id = $question_id;
        } else {
            $choiceData = $this->getChoiceByQuestionIdAndChoiceId($question_id, $choice_id);
        }
        $choiceData->choice_num = $new_choice_num;
        $choiceData->choice = $choice;
        $choiceData->other_choice_flg = $other_choice_flg ? $other_choice_flg : self::NOT_USE_OTHER_CHOICE;

        if($image_url !== '') {
            $choiceData->image_url = $image_url;
        }

        return $this->createChoice($choiceData);
    }

    public function createChoice($choiceData) {
        return $this->question_choice->save($choiceData);
    }

    public function createEmptyChoice() {
        return $this->question_choice->createEmptyObject();
    }

    /**
     * 選択肢を物理削除
     * @param $choice
     */
    public function deleteChoice($choice) {
        $this->question_choice->delete($choice);
    }

    /**
     * その他の選択肢の取得
     * @param $question_id
     * @return $question_choice
     */
    public function getOtherChoice($question_id) {
        $filter = array(
            'conditions' => array(
                'question_id' => $question_id,
                'other_choice_flg' => self::USE_OTHER_CHOICE,
            ),
        );
        return $this->question_choice->findOne($filter);
    }

    public function getOtherChoiceMapByQuestionIds($question_ids) {
        if ($question_ids === null || count($question_ids) === 0) {
            return array();
        }

        $filter = array(
            'where' => 'del_flg = 0 AND question_id IN(' . join(',', $question_ids) . ') AND other_choice_flg =' . self::USE_OTHER_CHOICE
        );
        $result = $this->question_choice->find($filter);
        $choice_map = array();
        foreach ($result as $row) {
            $choice_map[$row->question_id] = $row;
        }
        return $choice_map;
    }

    /**
     * その他の選択肢の取得
     * @param $question_ids
     * @return mixed
     */
    public function getOtherChoiceByQuestionIds($question_ids) {
        $filter = array(
            'conditions' => array(
                'question_id' => $question_ids,
                'other_choice_flg' => self::USE_OTHER_CHOICE
            )
        );

        return $this->question_choice->find($filter);
    }

    /**
     * 選択肢番号の最大値を取得
     * @param $question_id
     * @return $question_choice
     */
    public function getMaxChoiceNum($question_id) {
        $filter = array(
            'conditions' => array(
                'question_id' => $question_id,
                'other_choice_flg' => self::NOT_USE_OTHER_CHOICE,
            ),
            'order' => array(
                'name' => 'choice_num',
                'direction' => 'desc',
            ),
        );
        return $this->question_choice->findOne($filter)->choice_num;
    }

    /**
     * その他の選択肢の保存
     * @param $question_id
     */
    public function setOtherChoice($question_id) {
        $other_choice = $this->getOtherChoice($question_id);
        $choice_id = $other_choice ? $other_choice->id : 0;
        $new_choice_num = $this->getMaxChoiceNum($question_id) + 1;
        $this->setChoices($question_id, 'その他', $choice_id, $new_choice_num, '', self::USE_OTHER_CHOICE);
    }

    /**
     * その他の選択肢の削除
     * @param $question_id
     */
    public function deleteOtherChoice($question_id) {
        $choice = $this->getOtherChoice($question_id);
        if ($choice) {
            $this->deleteChoice($choice);
        }
    }

    /**
     * 選択肢の取得
     * @param $question_id
     * @return $question_choice
     */
    public function getChoicesByQuestionId($question_id) {
        $filter = array(
            'conditions' => array(
                'question_id' => $question_id,
            ),
            'order' => array(
                'name' => 'choice_num',
                'direction' => 'asc',
            ),
        );
        return $this->question_choice->find($filter);
    }

    public function getChoiceMapByQuestionIds($question_ids) {
        if ($question_ids === null || count($question_ids) === 0) {
            return array();
        }

        $filter = array(
            'where' => 'del_flg = 0 AND question_id IN(' . join(',', $question_ids) . ')',
            'order' => array(
                'name' => 'question_id, choice_num',
                'direction' => 'asc',
            ),
        );
        $result = $this->question_choice->find($filter);
        $choice_map = array();
        foreach ($result as $row) {
            if (!isset($choice_map[$row->question_id])) {
                $choice_map[$row->question_id] = array();
            }
            $choice_map[$row->question_id][] = $row;
        }
        return $choice_map;
    }

    public function getChoiceIdToChoiceMapByQuestionIds($question_ids) {
        if ($question_ids === null || count($question_ids) === 0) {
            return array();
        }

        $filter = array(
            'where' => 'del_flg = 0 AND question_id IN(' . join(',', $question_ids) . ')'
        );
        $result = $this->question_choice->find($filter);
        $choice_map = array();
        foreach ($result as $row) {
            $choice_map[$row->id] = $row;
        }
        return $choice_map;
    }

    /**
     * 選択肢の取得
     * @param $question_id
     * @param $choice_id
     * @return $question_choice
     */
    public function getChoiceByQuestionIdAndChoiceId($question_id, $choice_id) {
        $filter = array(
            'conditions' => array(
                'question_id' => $question_id,
                'id' => $choice_id,
            ),
        );
        return $this->question_choice->findOne($filter);
    }

    /**
     * 選択肢の取得
     * @param $choice_id
     * @return $question_choice
     */
    public function getChoiceById($choice_id) {
        $filter = array(
            'conditions' => array(
                'id' => $choice_id,
            ),
        );
        return $this->question_choice->findOne($filter);
    }

    /**
     * その他の選択肢を除いた選択肢の取得
     * @param $question_id
     * @return $question_choice
     */
    public function getChoicesExceptOtherChoiceByQuestionId($question_id) {
        $filter = array(
            'conditions' => array(
                'question_id' => $question_id,
                'other_choice_flg' => self::NOT_USE_OTHER_CHOICE,
            ),
            'order' => array(
                'name' => 'choice_num',
                'direction' => 'asc',
            ),
        );
        return $this->question_choice->find($filter);
    }

    /**
     * @param $questionnaires_questions_relation_id
     * @param $brands_users_relations_id
     * @param $question_id
     * @param $choice_id
     * @param $answer_text
     * @retunr $question_choice_answer
     */
    public function setQuestionChoiceAnswer($questionnaires_questions_relation_id, $brands_users_relation_id, $question_id, $choice_id, $answer_text = '') {
        $questionAnswer = $this->createEmptysetQuestionChoiceAnswer();
        $questionAnswer->brands_users_relation_id = $brands_users_relation_id;
        $questionAnswer->questionnaires_questions_relation_id = $questionnaires_questions_relation_id;
        $questionAnswer->question_id = $question_id;
        $questionAnswer->choice_id = $choice_id;
        $questionAnswer->answer_text = $answer_text;

        if ($this->choice_histories !== null) {
            $history = $this->createEmptysetQuestionChoiceAnswer();
            $history->brands_users_relation_id = $brands_users_relation_id;
            $history->questionnaires_questions_relation_id = $questionnaires_questions_relation_id;
            $history->question_id = $question_id;
            $history->choice_id = $choice_id;
            $history->answer_text = $answer_text;
            $history->submitted_at = $this->record_time;
            $this->choice_histories->save($history);
        }

        return $this->createQuestionChoiceAnswer($questionAnswer);
    }

    public function createQuestionChoiceAnswer($questionAnswer) {
        return $this->question_choice_answer->save($questionAnswer);
    }

    public function createEmptysetQuestionChoiceAnswer() {
        return $this->question_choice_answer->createEmptyObject();
    }

    public function getChoiceAnswers($brands_users_relation_id, $questionnaire_questions_relation_ids) {
        $filter = array(
                'where' => 'del_flg = 0 AND brands_users_relation_id = ' . $brands_users_relation_id .
                    ' AND questionnaires_questions_relation_id IN(' . join(',', $questionnaire_questions_relation_ids) . ')'
        );
        return $this->question_choice_answer->find($filter);
    }

    /**
     * 同じ回答を以前にしていないか取得
     * @param $brands_users_relation_id
     * @param $questionnaire_questions_relation_id
     * @return $question_choice_answer
     */
    public function existSingleAnswer($brands_users_relation_id, $questionnaire_questions_relation_id, $choice_id) {
        $filter = array(
            'conditions' => array(
                'brands_users_relation_id' => $brands_users_relation_id,
                'questionnaires_questions_relation_id' => $questionnaire_questions_relation_id,
                'choice_id' => $choice_id,
            ),
        );
        return $this->question_choice_answer->findOne($filter);
    }

    /**
     * 選択肢の回答を削除
     * @param $choice_answer
     */
    public function deleteChoiceAnswer($choice_answer) {
        $this->question_choice_answer->delete($choice_answer);
    }

    /**
     * 自由回答の回答を削除
     * @param $free_answer
     */
    public function deleteFreeAnswer($free_answer) {
        $this->question_free_answer->delete($free_answer);
    }

    /**
     * アンケートのユーザ情報より回答を取得
     * @param $brands_users_relation_id
     * @param $questionnaire_questions_relation_id
     * @return $answer_data
     */
    public function getChoiceAnswer($brands_users_relation_id, $questionnaire_questions_relation_id) {
        $filter = array(
            'conditions' => array(
                'brands_users_relation_id' => $brands_users_relation_id,
                'questionnaires_questions_relation_id' => $questionnaire_questions_relation_id,
            ),
        );
        $choice_answers = $this->question_choice_answer->find($filter);

        $answer_data = '';
        foreach ($choice_answers as $answer) {
            // その他じゃない選択肢の回答
            if ($this->isEmpty($answer->answer_text)) {
                $choice = $this->getChoiceById($answer->choice_id);
                if ($this->isEmpty($answer_data)) {
                    $answer_data = $choice->choice;
                } else {
                    $answer_data .= ','.$choice->choice;
                }
                // その他の選択肢の回答
            } else {
                if ($this->isEmpty($answer_data)) {
                    $answer_data = 'その他（' . $answer->answer_text . '）';
                } else {
                    $answer_data .= ','.'その他（' . $answer->answer_text . '）';
                }
            }
        }
        return $answer_data;
    }

    public function getChoiceImageAnswer($brands_users_relation_id, $questionnaire_questions_relation_id) {
        $filter = array(
            'conditions' => array(
                'brands_users_relation_id' => $brands_users_relation_id,
                'questionnaires_questions_relation_id' => $questionnaire_questions_relation_id,
            ),
        );
        $choice_answers = $this->question_choice_answer->find($filter);

        $answer_data = '';
        foreach ($choice_answers as $answer) {
            // その他じゃない選択肢の回答
            if ($this->isEmpty($answer->answer_text)) {
                $choice = $this->getChoiceById($answer->choice_id);
                if ($this->isEmpty($answer_data)) {
                    $answer_data = $choice->choice;
                    if($choice->image_url){
                        $answer_data .= '('.$choice->image_url.')';
                    }
                } else {
                    $answer_data .= ','.$choice->choice;
                    if($choice->image_url){
                        $answer_data .= '('.$choice->image_url.')';
                    }
                }
                // その他の選択肢の回答
            } else {
                if ($this->isEmpty($answer_data)) {
                    $answer_data = 'その他（' . $answer->answer_text . '）';
                } else {
                    $answer_data .= ','.'その他（' . $answer->answer_text . '）';
                }
            }
        }
        return $answer_data;
    }

    /**
     * 選択肢の設問の回答をすべて取得
     * @param $brands_users_relation_id
     * @param $questionnaire_questions_relation_id
     * @return $question_choice_answer
     */
    public function getSingleAndMultiChoiceAnswer($brands_users_relation_id, $questionnaires_questions_relation_id) {
        $filter = array(
            'conditions' => array(
                'brands_users_relation_id' => $brands_users_relation_id,
                'questionnaires_questions_relation_id' => $questionnaires_questions_relation_id,
            ),
        );
        return $this->question_choice_answer->find($filter);
    }

    public function getSingleAndMultiChoiceAnswerMap($brands_users_relation_id, $questionnaires_questions_relation_ids) {
        if (Util::isNullOrEmpty($brands_users_relation_id) || count($questionnaires_questions_relation_ids) === 0) {
            return array();
        }
        $filter = array(
            'where' =>  "del_flg = 0 AND brands_users_relation_id = {$brands_users_relation_id} AND " .
                "questionnaires_questions_relation_id IN(" . join(',', $questionnaires_questions_relation_ids) . ')'
        );
        $result = $this->question_choice_answer->find($filter);
        $choice_answer_map = array();
        foreach ($result as $row) {
            if (!isset($choice_answer_map[$row->questionnaires_questions_relation_id])) {
                $choice_answer_map[$row->questionnaires_questions_relation_id] = array();
            }
            $choice_answer_map[$row->questionnaires_questions_relation_id][$row->choice_id] = Util::isNullOrEmpty($row->answer_text) ? 1 : $row->answer_text;
        }
        return $choice_answer_map;
    }

    /**
     * アンケートのユーザ情報より単一式の回答を取得
     * @param $brands_users_relation_id
     * @param $questionnaire_questions_relation_id
     * @return $question_free_answer
     */
    public function getFreeAnswer($brands_users_relation_id, $questionnaire_questions_relation_id) {
        $filter = array(
            'conditions' => array(
                'brands_users_relation_id' => $brands_users_relation_id,
                'questionnaires_questions_relation_id' => $questionnaire_questions_relation_id,
            ),
        );
        return $this->question_free_answer->findOne($filter);
    }

    public function getFreeAnswerMap($brands_users_relation_id, $questionnaires_questions_relation_ids) {
        if (Util::isNullOrEmpty($brands_users_relation_id) || count($questionnaires_questions_relation_ids) === 0) {
            return array();
        }
        $filter = array(
            'where' =>  "del_flg = 0 AND brands_users_relation_id = {$brands_users_relation_id} AND " .
                "questionnaires_questions_relation_id IN(" . join(',', $questionnaires_questions_relation_ids) . ')'
        );
        $result = $this->question_free_answer->find($filter);
        $free_answer_map = array();
        foreach ($result as $row) {
            if (!isset($free_answer_map[$row->questionnaires_questions_relation_id])) {
                $free_answer_map[$row->questionnaires_questions_relation_id] = array();
            }
            $free_answer_map[$row->questionnaires_questions_relation_id] = $row->answer_text;
        }
        return $free_answer_map;
    }

    /**
     * @param $questionnaires_questions_relation_id
     * @param $brands_users_relations_id
     * @param $question_id
     * @param $answer_text
     * @return $question_free_answer
     */
    public function setQuestionFreeAnswer($questionnaires_questions_relation_id, $brands_users_relation_id, $question_id, $answer_text) {
        $questionAnswer = $this->createEmptysetQuestionFreeAnswer();
        $questionAnswer->brands_users_relation_id = $brands_users_relation_id;
        $questionAnswer->questionnaires_questions_relation_id = $questionnaires_questions_relation_id;
        $questionAnswer->question_id = $question_id;
        $questionAnswer->answer_text = $answer_text;

        if ($this->free_histories !== null) {
            $history = $this->createEmptysetQuestionFreeAnswer();
            $history->brands_users_relation_id = $brands_users_relation_id;
            $history->questionnaires_questions_relation_id = $questionnaires_questions_relation_id;
            $history->question_id = $question_id;
            $history->answer_text = $answer_text;
            $history->submitted_at = $this->record_time;
            $this->free_histories->save($history);
        }

        return $this->createQuestionFreeAnswer($questionAnswer);
    }

    public function createQuestionFreeAnswer($questionAnswer) {
        return $this->question_free_answer->save($questionAnswer);
    }

    public function createEmptysetQuestionFreeAnswer() {
        return $this->question_free_answer->createEmptyObject();
    }

    /**
     * アンケートの質問取得
     * @param $questionnaire_action_id
     * @return $questionnaire_questions
     */
    public function getQuestionsByQuestionnaireActionId($questionnaire_action_id) {
        $questionnaire_questions_relations = $this->getRelationsByQuestionnaireActionId($questionnaire_action_id);
        foreach ($questionnaire_questions_relations as $relation) {
            $questionnaire_questions[] = $this->getQuestionById($relation->question_id);
        }
        return $questionnaire_questions;
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getProfileQuestionRelationsByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id
            ),
            'order' => array(
                'name' => 'number',
                'direction' => 'asc'
            )
        );
        return $this->questionnaires_questions_relation->find($filter);
    }

    public function getProfileQuestionsByBrandId($brand_id) {
        $relations = $this->getProfileQuestionRelationsByBrandId($brand_id);
        $questions = array();
        foreach ($relations as $relation) {
            $questions[] = $this->getQuestionById($relation->question_id);
        }
        return $questions;
    }

    // TODO public => 1 つけたい
    public function getPublicProfileQuestionRelationByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id
            ),
            'order' => array(
                'name' => 'number',
                'direction' => 'asc'
            )
        );
        return $this->questionnaires_questions_relation->find($filter);
    }

    public function getPublicProfileQuestionRelationsByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'public' => 1
            ),
            'order' => array(
                'name' => 'number',
                'direction' => 'asc'
            )
        );
        return $this->questionnaires_questions_relation->find($filter);
    }

    /**
     * キャンペーンから登録する導線で、エントリー・モジュールで取得するカスタム・プロフィール・アンケートを取得します。
     *
     * @param $brand_id
     * @param $cp_action_id
     * @return mixed
     */
    public function getEntryActionProfileQuestionRelationsByBrandIdAndCpActionId($brand_id, $cp_action_id) {
        if (Util::existNullOrEmpty($brand_id, $cp_action_id)) {
            return null;
        }
        $filter = array(
            'where' => 'brand_id =' . $brand_id . ' AND '.
                '(public = 1 OR question_id IN' .
                '(' .
                'SELECT profile_questionnaire_id ' .
                'FROM cp_profile_questionnaires WHERE del_flg = 0 AND ' .
                'cp_action_id = ' . $cp_action_id . ''.
                ')'.
                ') AND del_flg = 0',
            'order' => array(
                'name' => 'number',
                'direction' => 'asc'
            )
        );
        return $this->questionnaires_questions_relation->find($filter);
    }

    /**
     * エントリー・モジュールで再取得するカスタム・プロフィール・アンケートを取得します。
     *
     * @param $brand_id
     * @param $cp_action_id
     * @return mixed
     */
    public function getResendEntryActionProfileQuestionRelationsByBrandIdAndCpActionId($brand_id, $cp_action_id) {
        if (Util::isNullOrEmpty($brand_id) || Util::isNullOrEmpty($cp_action_id)) {
            return null;
        }

        $filter = array(
            'where' => 'brand_id =' . $brand_id . ' AND '.
                'question_id IN' .
                '(' .
                'SELECT profile_questionnaire_id ' .
                'FROM cp_profile_questionnaires WHERE del_flg = 0 AND ' .
                'cp_action_id = ' . $cp_action_id . ''.
                ')'.
                'AND del_flg = 0',
            'order' => array(
                'name' => 'number',
                'direction' => 'asc'
            )
        );
        return $this->questionnaires_questions_relation->find($filter);
    }

    /**
     * signup時に必要なカスタム・プロフィール・アンケートを取得します。
     * @param $brand_id
     * @return mixed
     */
    public function getSignupProfileQuestionRelationByBrandId($brand_id) {
        if (Util::isNullOrEmpty($brand_id)) {
            return array();
        }
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'public' => 1
            ),
            'order' => array(
                'name' => 'number',
                'direction' => 'asc'
            )
        );
        return $this->questionnaires_questions_relation->find($filter);
    }

    /**
     * @param $brands_users_relation_id
     * @param $questionnaire_questions_relation_id
     * TODO 移行後に削除
     */
    public function existChoiceAnswer($brands_users_relation_id, $questionnaire_questions_relation_id) {
        $filter = array(
            'conditions' => array(
                'brands_users_relation_id' => $brands_users_relation_id,
                'questionnaires_questions_relation_id' => $questionnaire_questions_relation_id,
            ),
        );
        return $this->question_choice_answer->findOne($filter);
    }

    /**
     * @param $brands_users_relation_id
     * @param $questionnaire_questions_relation_id
     * @param $choice_id
     * TODO 移行後に削除
     */
    public function existSameChoiceAnswer($brands_users_relation_id, $questionnaire_questions_relation_id, $choice_id) {
        $filter = array(
            'conditions' => array(
                'brands_users_relation_id' => $brands_users_relation_id,
                'questionnaires_questions_relation_id' => $questionnaire_questions_relation_id,
                'choice_id' => $choice_id
            ),
        );
        return $this->question_choice_answer->findOne($filter);
    }

    /**
     * @param $question_id
     * @param $choice
     * @param $new_choice_num
     * TODO 移行後に削除
     */
    public function getSameChoice($question_id, $new_choice_num) {
        $filter = array(
            'conditions' => array(
                'question_id' => $question_id,
                'choice_num' => $new_choice_num
            ),
        );
        return $this->question_choice->findOne($filter);
    }

    /**
     * ユーザーの答えを削除する
     *
     * @param $questionnaire_action_id
     */
    public function deletePhysicalUserAnswerByQuestionnaireActionId ($questionnaire_action_id) {

        if (!$questionnaire_action_id) {
            throw new Exception("CpQuestionnaireService#deletePhysicalUserAnswerByQuestionnaireActionId questionnaire_action_id null");
        }

        $questionnaire_relations = $this->getRelationsByQuestionnaireActionId($questionnaire_action_id);

        if (!$questionnaire_relations) {
            throw new Exception("CpQuestionnaireService#deletePhysicalUserAnswerByQuestionnaireActionId questionnaire_relations null");
        }

        foreach ($questionnaire_relations as $questionnaire_relation) {
            $users_answers = $this->question_choice_answer->find(array('questionnaires_questions_relation_id' => $questionnaire_relation->id));
            if ($users_answers) {
                foreach ($users_answers as $users_answer) {
                    $this->question_choice_answer->deletePhysical($users_answer);
                }
            } else {
                $users_answers = $this->question_free_answer->find(array('questionnaires_questions_relation_id' => $questionnaire_relation->id));
                foreach ($users_answers as $users_answer) {
                    $this->question_free_answer->deletePhysical($users_answer);
                }
            }
        }
    }

    public function deletePhysicalUserAnswerByQuestionnaireActionIdAndBrandUserRelation ($questionnaire_action_id, $brand_user_relation_id)
    {

        if (!$questionnaire_action_id || !$brand_user_relation_id) {
            throw new Exception("CpQuestionnaireService#deletePhysicalUserAnswerByQuestionnaireActionId null");
        }

        $questionnaire_relations = $this->getRelationsByQuestionnaireActionId($questionnaire_action_id);

        if (!$questionnaire_relations) {
            throw new Exception("CpQuestionnaireService#deletePhysicalUserAnswerByQuestionnaireActionId questionnaire_relations null");
        }

        foreach ($questionnaire_relations as $questionnaire_relation) {
            $users_answers = $this->question_choice_answer->find(array('questionnaires_questions_relation_id' => $questionnaire_relation->id, 'brands_users_relation_id' => $brand_user_relation_id));
            if ($users_answers) {
                foreach ($users_answers as $users_answer) {
                    $this->question_choice_answer->deletePhysical($users_answer);
                }
            } else {
                $users_answers = $this->question_free_answer->find(array('questionnaires_questions_relation_id' => $questionnaire_relation->id, 'brands_users_relation_id' => $brand_user_relation_id));
                foreach ($users_answers as $users_answer) {
                    $this->question_free_answer->deletePhysical($users_answer);
                }
            }
        }
    }

    /**
     * 選択肢が存在しない設問、公開しておらずエントリーアクションでも使用されていない設問を省くためのメソッド
     * @param $profile_question_relations
     */
    public function useProfileQuestion($profile_question_relations) {
        $use_profile_questions = array();
        /** @var CpEntryProfileQuestionnaireService $cp_entry_profile_service */
        $cp_entry_profile_service = $this->getService('CpEntryProfileQuestionnaireService');
        foreach($profile_question_relations as $relation) {
            $profile_question = $this->getQuestionById($relation->question_id);
            if($profile_question->type_id != QuestionTypeService::FREE_ANSWER_TYPE && !$this->getChoicesByQuestionId($relation->question_id)) {
                continue;
            }
            if($relation->public) {
                $use_profile_questions[] = $relation;
            } elseif($cp_entry_profile_service->getQuestionnairesByProfileQuestionnaireId($relation->id)) {
                $use_profile_questions[] = $relation;
            }
        }
        return $use_profile_questions;
    }

    /**
     * @param $search_conditions
     * @param $search_params
     * @param $order
     * @return array
     */
    public function getUserListCondition($search_conditions, $search_params, $order) {
        $condition = array(
            'brand_id' => $search_conditions['brand_id'],
            'cp_action_id' => $search_conditions['cp_action_id']
        );

        if (!empty($search_params['approval_status'])) {
            if ($search_params['approval_status'] == QuestionnaireUserAnswer::APPROVAL_STATUS_UNAPPROVED) {
                $condition['PRE_APPROVAL'] = '__ON__';
            } else {
                $condition['POST_APPROVAL'] = '__ON__';
                $condition['approval_status'] = $search_params['approval_status'];
            }
        }

        if ($order) {
            if ($order['name'] == "created_at" && $order['direction'] == "desc") {
                $condition['CREATED_AT_DESC'] = '__ON__';
            } elseif ($order['name'] == "cp_user_id" && $order['direction'] == "desc") {
                $condition['CP_USER_ID_DESC'] = '__ON__';
            }
        }

        return $condition;
    }

    /**
     * @param $search_conditions
     * @param $pager
     * @param $search_params
     * @param $order
     * @return mixed
     */
    public function getUserAnswerList($search_conditions, $pager, $search_params, $order) {
        $db = aafwDataBuilder::newBuilder();
        $conditions = $this->getUserListCondition($search_conditions, $search_params, $order);

        $user_answer_list = $db->getUserAnswerList($conditions, null, $pager, true);

        return $user_answer_list;
    }

    /**
     * @param $bur_ids
     * @param $question_ids
     * @return mixed
     */
    public function getChoiceAnswersByBurIds($bur_ids, $question_ids) {
        $filter = array(
            'where' => 'del_flg = 0 AND brands_users_relation_id IN( ' . join(',', $bur_ids) . ')' .
                ' AND question_id IN(' . join(',', $question_ids) . ')'
        );
        return $this->question_choice_answer->find($filter);
    }

    /**
     * @param $bur_ids
     * @param $question_ids
     * @return mixed
     */
    public function getFreeAnswersByBurIds($bur_ids, $question_ids) {
        $filter = array(
            'where' => 'del_flg = 0 AND brands_users_relation_id IN( ' . join(',', $bur_ids) . ')' .
                ' AND question_id IN(' . join(',', $question_ids) . ')'
        );
        return $this->question_free_answer->find($filter);
    }
}

