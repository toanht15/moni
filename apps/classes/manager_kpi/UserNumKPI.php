<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');

class UserNumKPI implements IManagerKPI {

    function doExecute($date) {
        $users = aafwEntityStoreFactory::create('Users');
        $filter = array(
            'created_at:<' => date('Y-m-d', strtotime($date . '+1 day')),
        );
        return $users->count($filter);
    }
}
