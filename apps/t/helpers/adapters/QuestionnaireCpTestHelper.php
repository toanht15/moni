<?php
AAFW::import('jp.aainc.t.testcases.CpBaseTest');

class QuestionnaireCpTestHelper extends CpBaseTest {

    public function saveAction(CpActions $cp_action, $condition = null) {
        if (!$cp_action) return;

        if ($condition['cp_questionnaire_actions']) {
            $cp_concrete_action = $this->updateCpConcreteAction('CpQuestionnaireActions', $cp_action->id, $condition['cp_questionnaire_actions']);
        } else {
            $cp_concrete_action = $this->getCpConcreteAction('CpQuestionnaireActions', $cp_action->id);
        }
        if (!$cp_concrete_action) return;

        $questionnaire = $this->createQuestionnaire($cp_concrete_action->id, $condition);
        $questionnaire_question = $this->createQuestionnaireRelation($cp_concrete_action->id, $questionnaire_relation);

        return array('cp_concrete_action' => $cp_concrete_action, 'instant_win_prizes' => $instant_win_prizes);
    }

    protected function createQuestionnaireRelation($cp_concrete_action_id, $condition = null) {
        AAFW::import ('jp.aainc.aafw.classes.services.CpQuestionnaireService');

        if (!$condition['number']) return;
        if($condition['question_type'] == QuestionTypeService::FREE_ANSWER_TYPE) {
            $question = $this->entity(
                'QuestionnaireQuestions',
                array(
                    'type_id' => QuestionTypeService::FREE_ANSWER_TYPE,
                    'question' => '自由回答テスト'
                )
            );
        } elseif(QuestionTypeService::isChoiceQuestion($condition['question_type'])) {
            $question = $this->entity(
                'QuestionnaireQuestions',
                array(
                    'type_id' => $condition['question_type'],
                    'question' => '選択回答テスト'
                )
            );

            $choice_requirement = $this->entity(
                'QuestionChoiceRequirements',
                array(
                    'question_id' => $question->id,
                    'use_other_choice_flg' => $condition['use_other_choice_flg'] ?: CpQuestionnaireService::NOT_USE_OTHER_CHOICE,
                    'random_order_flg' => $condition['random_order_flg'] ?: CpQuestionnaireService::RANDOM_ORDER,
                    'multi_answer_flg' => $condition['multi_answer_flg'] ?: CpQuestionnaireService::MULTI_ANSWER
                )
            );

            // 選択肢はデフォルトで2つ作成
            $choice1 = $this->entity(
                'QuestionChoices',
                array(
                    'question_id' => $question->id,
                    'choice_num' => 1,
                    'choice' => 'テスト選択肢1',
                    'other_choice_flg' => $condition['other_choice_flg'] ?: CpQuestionnaireService::NOT_USE_OTHER_CHOICE
                )
            );
            $choice2 = $this->entity(
                'QuestionChoices',
                array(
                    'question_id' => $question->id,
                    'choice_num' => 2,
                    'choice' => 'テスト選択肢2',
                    'other_choice_flg' => $condition['other_choice_flg'] ?: CpQuestionnaireService::NOT_USE_OTHER_CHOICE
                )
            );
        }
        $new_questionnaire = $this->entity(
            'QuestionnairesQuestionsRelations',
            array(
                'brand_id' => $brand->id,
                'question_id' => $question->id,
                'requirement_flg' => $condition['requirement_flg'] ?: CpQuestionnaireService::QUESTION_NOT_REQUIRED,
                'number' => $condition['number'],
                'public' => $condition['public'] ?: 0,
            )
        );
        return $new_questionnaire;
    }


    public function joinAction(CpActions $cp_action, CpUsers $cp_user, $prize_status = InstantWinUsers::PRIZE_STATUS_WIN) {
        if (!$cp_action || !$cp_user) return;

        $cp_concrete_action = $this->findOne('CpInstantWinActions', array('cp_action_id' => $cp_action->id));
        if (!$cp_concrete_action) return;
        $instant_win_prize = $this->findOne('InstantWinPrizes', array('cp_concrete_action' => $cp_concrete_action->id, 'prize_status' => $prize_status));
        if (!$instant_win_prize) return;

        list($instant_win_user, $instant_win_user_log) = $this->drawInstantWin($cp_action->id, $cp_user->id, $instant_win_prize->id, $prize_status);

        $action_status = $prize_status == InstantWinUsers::PRIZE_STATUS_WIN ? null : CpUserActionStatus::NOT_JOIN ;
        $this->updateCpUserActionMessageAndStatus($cp_concrete_action->cp_action_id, $cp_user->id, $action_status);
        return array('instant_win_user' => $instant_win_user, 'instant_win_user_log' => $instant_win_user_log);
    }

    protected function drawInstantWin($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status) {
        $instant_win_user = $this->findOne('InstantWinUsers',
            array(
                'cp_action_id' => $cp_action_id,
                'cp_user_id' => $cp_user_id,
                'instant_win_prize_id' => $instant_win_prize_id,
                'prize_status' => $prize_status
            )
        );
        if (!$instant_win_user) {
            $instant_win_user = $this->createInstantWinUser($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status);
        } else {
            $instant_win_user = $this->updateInstantWinUser($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status);
        }

        $instant_win_user_log = $this->createInstantWinUserLog($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status);
        return array($instant_win_user, $instant_win_user_log);
    }

    protected function createInstantWinUser($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status) {
        $instant_win_user = $this->entity(
            'InstantWinUsers',
            array(
                'cp_action_id' => $cp_action_id,
                'cp_user_id' => $cp_user_id,
                'instant_win_prize_id' => $instant_win_prize_id,
                'prize_status' => $prize_status
            )
        );
        return $instant_win_user;
    }

    protected function updateInstantWinUser($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status) {
        $instant_win_user = $this->updateEntities(
            'InstantWinUsers',
            array(
                'cp_action_id' => $cp_action_id,
                'cp_user_id' => $cp_user_id,
                'instant_win_prize_id' => $instant_win_prize_id,
            ),
            array(
                'prize_status' => $prize_status
            )
        );
        return $instant_win_user;
    }

    protected function createInstantWinUserLog($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status) {
        $instant_win_user_log = $this->entity(
            'InstantWinUserLogs',
            array(
                'cp_action_id' => $cp_action_id,
                'cp_user_id' => $cp_user_id,
                'instant_win_prize_id' => $instant_win_prize_id,
                'prize_status' => $prize_status
            )
        );
        return $instant_win_user_log;
    }
} 