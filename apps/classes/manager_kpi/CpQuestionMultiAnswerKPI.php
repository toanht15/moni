<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class CpQuestionMultiAnswerKPI implements IManagerKPI {

    function doExecute() {
        list($date) = func_get_args();
        $filter = array(
            'created_at_start' => date("Y-m-d 00:00:00", strtotime($date)),
            'created_at_end' => date("Y-m-d 23:59:59", strtotime($date)),
            'multi_answer_flg' => CpQuestionnaireService::MULTI_ANSWER
        );

        // メンテDBから取得
        $mainte_db = new aafwDataBuilder('maintedb');
        $cpQuestionMultiAnswer = $mainte_db->getCountCpQuestionChoiceAnswer($filter);

        return $cpQuestionMultiAnswer[0]['total'];
    }
}