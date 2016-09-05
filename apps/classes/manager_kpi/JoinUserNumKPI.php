<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');

class JoinUserNumKPI implements IManagerKPI {

    function doExecute($date) {
        $users = aafwEntityStoreFactory::create('Users');
        $filter = array(
            'created_at:<' => date('Y-m-d', strtotime($date . '+1 day')),
            'created_at:>=' => date('Y-m-d', strtotime($date)),
        );
        return $users->count($filter);
    }
}
