<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.entities.Cp');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.entities.CpUserActionStatus');

class JoinCpUserNumSumKPI implements IManagerKPI {

    function doExecute() {
        list($date, $brandId) = func_get_args();
        $filter = array(
            'created_at_end'   => date("Y-m-d 23:59:59", strtotime($date)),
            'type'             => Cp::TYPE_CAMPAIGN,
            'status'           => CpUserActionStatus::JOIN,
            'action_type'      => CpAction::$legal_opening_cp_actions
        );
        if ($brandId) {
            $filter += array(
                'brand_id' => $brandId,
            );
        }

        // メンテDBから取得
        $db = aafwDataBuilder::newBuilder('maintedb');
        $cpUsers = $db->getKPIofJoinCPUserNumSum($filter);

        return $cpUsers[0]['numbers'];
    }
}