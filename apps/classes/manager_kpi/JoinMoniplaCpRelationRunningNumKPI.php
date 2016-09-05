<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.entities.BrandsUsersRelation');

class JoinMoniplaCpRelationRunningNumKPI implements IManagerKPI {

    function doExecute($date) {
        $BrandsUsersRelation = aafwEntityStoreFactory::create('BrandsUsersRelations');
        $filter = array(
            'created_at:<' => date('Y-m-d', strtotime($date .' +1 day')),
            'created_at:>=' => date('Y-m-d', strtotime($date)),
            'from_kind' => BrandsUsersRelationService::FROM_KIND_CAMPAIGN,
        );
        return $BrandsUsersRelation->count($filter);
    }
}