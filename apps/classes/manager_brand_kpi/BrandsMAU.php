<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.entities.BrandsUsersRelation');

class BrandsMAU implements IManagerKPI {

    function doExecute($date) {
        list($date, $brandId) = func_get_args();
        $filter = array(
            'created_at_start' => date('Y-m-d', strtotime($date . '-30 day')),
            'created_at_end' => date('Y-m-d', strtotime($date)),
            'BRAND_KPI' => '__ON__',
            'brand_id' => $brandId
        );

        // メンテDBから取得
        $mainte_db = new aafwDataBuilder('maintedb');
        $mau = $mainte_db->getCountMauByLastLoginDate($filter);

        return $mau[0]['mau'];
    }
}