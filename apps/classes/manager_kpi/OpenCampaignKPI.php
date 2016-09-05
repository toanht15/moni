<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.services.BrandService');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class OpenCampaignKPI implements IManagerKPI {

    function doExecute() {
        list($date) = func_get_args();
        //TODO 先着順＋スピードズジ対応
        $filter = array(
            'preriod_time' => date("Y-m-d 23:59:59", strtotime($date)),
            'status' => Cp::CAMPAIGN_STATUS_OPEN,
            'test_page' => BrandService::TEST,
        );

        // メンテDBから取得
        $mainte_db = new aafwDataBuilder('maintedb');
        $OpendCampaign = $mainte_db->getOpenedCampaigns($filter);

        return $OpendCampaign[0]['total'];
    }
}