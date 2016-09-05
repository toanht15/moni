<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class MAU implements IManagerKPI {

    function doExecute($date) {
        $filter = array(
            'created_at_start' => date('Y-m-d', strtotime($date . '-30 day')),
            'created_at_end' => date('Y-m-d', strtotime($date)),
        );

        // メンテDBから取得
        $mainte_db = new aafwDataBuilder('maintedb');
        $mau = $mainte_db->getCountMauByLastLoginDate($filter);

        return $mau[0]['mau'];
    }
}
