<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class JoinCpUserMAUBetween30DaysKPI implements IManagerKPI {

    function doExecute($date) {
        $filter = array(
            'created_at_start' => date("Y-m-d 00:00:00", strtotime($date. '-30day')),
            'created_at_end' => date("Y-m-d 23:59:59", strtotime($date)),
        );

        // メンテDBから取得
        $mainte_db = new aafwDataBuilder('maintedb');
        $join_cp_user_mau_between_30_days_kpi = $mainte_db->getJoinUUCpUserByDate($filter);
        return $join_cp_user_mau_between_30_days_kpi[0]['total'];
    }
}