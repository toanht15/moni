<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.services.BrandPageSettingService');
AAFW::import('jp.aainc.classes.services.BrandService');

class CompanyPublicKPI implements IManagerKPI {

    function doExecute() {
        $condition = array();
        $condition['PUBLIC_FLG'] = '__ON__';
        $condition['TEST'] = '__ON__';
        $condition['type'] = BrandPageSettingService::STATUS_PUBLIC;
        $condition['test_page'] =  BrandService::COMPANY;
        $condition['__NOFETCH__'] = true;

        // メンテDBから取得
        $mainte_db = new aafwDataBuilder('maintedb');
        $CompanyPublic = $mainte_db->getBrandSearch($condition, array(), array(), true);

        return $CompanyPublic['pager']['count'];

    }
}
