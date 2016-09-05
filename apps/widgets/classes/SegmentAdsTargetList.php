<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class SegmentAdsTargetList extends aafwWidgetBase {

    public function doService($params = array()) {

        /** @var SegmentService $segment_service */
        $segment_service = $this->getService('SegmentService');

        $params['sp_data'] = array();
        $previous_date = strtotime('yesterday');
        $total_users_count = null;
        $total_prev_users_count = 0;

        foreach($params['provision_ids'] as $provision_id) {
            $cur_users_count = $segment_service->getSegmentProvisionUsersCountByDate($provision_id);
            $prev_users_count = $segment_service->getSegmentProvisionUsersCountByDate($provision_id, $previous_date);

            if($total_users_count == null && $cur_users_count->total != 0) {
                $total_users_count = $cur_users_count->total;
            } elseif($total_users_count != null) {
                $total_users_count += $cur_users_count->total;
            }
            
            $total_prev_users_count += $prev_users_count->total;

            $params['sp_data'][$provision_id]['counter_text'] = SegmentProvisionUsersCount::getUserCounterText($cur_users_count->total, $prev_users_count->total);
            $params['sp_data'][$provision_id]['value'] = $segment_service->getSegmentProvisionById($provision_id);

            if($params['sp_data'][$provision_id]['value']->name == '') {
                $segment = $segment_service->getSegmentById($params['sp_data'][$provision_id]['value']->segment_id);
                $params['sp_data'][$provision_id]['value']->name = $segment->name;
            }
        }

        $params['sp_data']['total']['counter_text'] = SegmentProvisionUsersCount::getUserCounterText($total_users_count, $total_prev_users_count);
        $params['sp_data']['total']['duplicate'] = '-';

        if($params['sp_data']['total']['counter_text'] != '-') {

            $cur_date = strtotime('today');

            $created_dates = array($previous_date, $cur_date);

            $target_user_count = $segment_service->calculateSegmentProvisionUserCount($params['provision_ids'], $created_dates);

            $params['sp_data']['total']['duplicate'] = $params['sp_data']['total']['counter_text'] - $target_user_count;
        }

        return $params;
    }
}
