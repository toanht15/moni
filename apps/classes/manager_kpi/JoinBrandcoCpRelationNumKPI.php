<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.entities.BrandsUsersRelation');

class JoinBrandcoCpRelationNumKPI implements IManagerKPI {

    function doExecute() {
        list($date, $brandId) = func_get_args();
        $BrandsUsersRelation = aafwEntityStoreFactory::create('BrandsUsersRelations');
        $filter = array(
            'created_at:<' => date('Y-m-d', strtotime($date .' +1 day')),
            'created_at:>=' => date('Y-m-d', strtotime($date)),
            'from_kind' => BrandsUsersRelationService::FROM_KIND_BRANDCO,
        );
        if($brandId){
            $filter['brand_id'] = $brandId;
        }
        return $BrandsUsersRelation->count($filter, 'user_id');
    }
}