<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');

class OptInFlgKPI implements IManagerKPI {

    function doExecute($date) {
        $BrandsUsersRelation = aafwEntityStoreFactory::create('BrandsUsersRelations');
        $filter = array(
            'created_at:<' => date('Y-m-d', strtotime($date . '+1 day')),
            'optin_flg' => BrandsUsersRelationService::STATUS_OPTIN,
        );
        return $BrandsUsersRelation->count($filter);
    }
}