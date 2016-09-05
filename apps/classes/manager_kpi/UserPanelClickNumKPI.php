<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');

class UserPanelClickNumKPI implements IManagerKPI {

    function doExecute($date) {
        $user_panel_clicks = aafwEntityStoreFactory::create('UserPanelClicks');
        $filter = array(
            'created_at:<' => date('Y-m-d', strtotime($date . '+1 day')),
            'created_at:>=' => date('Y-m-d', strtotime($date)),
        );
        return $user_panel_clicks->count($filter);
    }
}
