<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class SegmentProvisionTooltipList extends aafwWidgetBase {

    public function doService($params = array()) {

        /** @var SegmentService $segment_service */
        $segment_service = $this->getService('SegmentService');
        $params['segment_provisions'] = $segment_service->getSegmentProvisionsBySegmentId($params['segment']->id);
        $params['segment_provision_conditional'] = $params['segment']->isConditionalSegment() ? $params['segment_provisions']->current() : null;

        if ($params['segment']->isActive()) {

            $total_users_count = 0;
            $total_prev_users_count = 0;
            $params['sp_data'] = array();
            $previous_date = strtotime('yesterday');

            foreach ($params['segment_provisions'] as $sp) {
                $cur_users_count = $segment_service->getSegmentProvisionUsersCountByDate($sp->id);
                $prev_users_count = $segment_service->getSegmentProvisionUsersCountByDate($sp->id, $previous_date);

                $total_users_count += $cur_users_count->total;
                $total_prev_users_count += $prev_users_count->total;

                $params['sp_data'][$sp->id]['counter_text'] = SegmentProvisionUsersCount::getUserCounterText($cur_users_count->total, $prev_users_count->total);
            }

            $params['sp_data']['total']['counter_text'] = SegmentProvisionUsersCount::getUserCounterText($total_users_count, $total_users_count);
        }

        return $params;
    }

}
