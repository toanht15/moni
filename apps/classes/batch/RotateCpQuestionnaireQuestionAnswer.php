<?php

AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
require_once dirname(__FILE__) . '/../../config/define.php';

class RotateCpQuestionnaireQuestionAnswer {

    public function doProcess($date) {

        /** @var aafwDataBuilder $dataBuilder */
        $dataBuilder = aafwDataBuilder::newBuilder();

        // 前日の日付
        $target_date = date('Y-m-d', strtotime($date));
        $cp_questionnaire_question_answers = $dataBuilder->countCpQuestionnaireQuestionAnswers(array(
            'target_date' => $target_date,
        ));

        $sql = $this->buildInsertOrUpdateSql($cp_questionnaire_question_answers);

        $store = aafwEntityStoreFactory::create('CpQuestionnaireQuestionAnswerSummaries');
        try {
            $store->begin();

            $dataBuilder->executeUpdate($sql);

            $store->commit();
        } catch (Exception $e) {
            $store->rollback();

            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param array $cp_questionnaire_question_answers
     * @return string
     * @throws Exception
     */
    private function buildInsertOrUpdateSql($cp_questionnaire_question_answers = array()) {
        $values = array();
        foreach ($cp_questionnaire_question_answers as $cp_questionnaire_question_answer) {
            if (
                !$cp_questionnaire_question_answer['cp_action_id'] ||
                !$cp_questionnaire_question_answer['questionnaire_question_id'] ||
                is_null($cp_questionnaire_question_answer['question_choice_id'])
            ) {
                throw new Exception('Error: invalid values.');
            }

            $tmp = array();
            $tmp[] = $cp_questionnaire_question_answer['cp_action_id'];
            $tmp[] = $cp_questionnaire_question_answer['questionnaire_question_id'];
            $tmp[] = $cp_questionnaire_question_answer['question_choice_id'];
            $tmp[] = $cp_questionnaire_question_answer['n_answers'] ?: 0;
            $tmp[] = '"' . date("Y-m-d H:i:s") . '"';
            $tmp[] = '"' . date("Y-m-d H:i:s") . '"';

            $values[] = '(' . implode(', ', $tmp) . ')';
        }

        $sql = "INSERT INTO cp_questionnaire_question_answer_summaries (`cp_action_id`, `question_id`, `question_choice_id`, `n_answers`, `created_at`, `updated_at`) VALUE ";
        $sql .= implode(', ', $values) . ' ';
        $sql .= "ON DUPLICATE KEY UPDATE n_answers = VALUES(n_answers), updated_at = " . '"' . date("Y-m-d H:i:s") . '"';

        return $sql;
    }
}

