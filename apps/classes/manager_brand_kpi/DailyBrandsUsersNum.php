<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.entities.BrandsUsersRelation');

class DailyBrandsUsersNum implements IManagerKPI {

    function doExecute() {
        list($date, $brandId) = func_get_args();
        $BrandsUsersRelation = aafwEntityStoreFactory::create('BrandsUsersRelations');
        $filter = array(
            'created_at:<' => date('Y-m-d', strtotime($date .' +1 day')),
            'created_at:>=' => date('Y-m-d', strtotime($date)),
        );
        if($brandId){
            $filter['brand_id'] = $brandId;
        }
        return $BrandsUsersRelation->count($filter, 'user_id');
    }
}