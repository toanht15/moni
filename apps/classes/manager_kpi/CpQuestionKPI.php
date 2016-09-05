<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class CpQuestionKPI implements IManagerKPI {

    function doExecute() {
        list($date) = func_get_args();
        $filter = array(
            'period_date' => date("Y-m-d", strtotime($date))
        );

        // メンテDBから取得
        $mainte_db = new aafwDataBuilder('maintedb');
        $cpQuestion = $mainte_db->getCountCpQuestion($filter);

        return $cpQuestion[0]['total'];
    }
}