<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class ProfileQuestionFreeAnswerKPI implements IManagerKPI {

    function doExecute() {
        list($date) = func_get_args();

        $filter = array(
            'conditions' => array(
                'created_at:>=' => date("Y-m-d 00:00:00", strtotime($date)),
                'created_at:<=' => date("Y-m-d 23:59:59", strtotime($date))
            ),
        );
        $question_free_answer = aafwEntityStoreFactory::create('ProfileQuestionFreeAnswers');

        return $question_free_answer->count($filter);
    }
}