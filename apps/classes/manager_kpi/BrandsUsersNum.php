<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');

class BrandsUsersNum implements IManagerKPI {

    function doExecute($date) {
        $users = aafwEntityStoreFactory::create('BrandsUsersRelations');
        $filter = array(
            'created_at:<' => date('Y-m-d', strtotime($date . '+1 day')),
        );
        return $users->count($filter);
    }
}
