<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.SocialAccountService');

class SegmentContainer extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var SegmentService $segment_service */
        $segment_service = $this->getService('SegmentService');
        /** @var SegmentCreateSqlService $create_sql_service */
        $create_sql_service = $this->getService('SegmentCreateSqlService');

        $params['segment_url'] = $this->getSegmentUrl($params['segment']);
        $params['segment_provisions'] = $segment_service->getSegmentProvisionsBySegmentId($params['segment']->id);

        $params['segment_provision_conditional'] = $params['segment']->isConditionalSegment() ? $params['segment_provisions']->current() : null;

        if ($params['segment']->isActive()) {
            $segment_status = null;
            $total_users_count = 0;
            $total_prev_users_count = 0;
            $params['sp_data'] = array();
            $previous_date = strtotime('yesterday');

            foreach ($params['segment_provisions'] as $sp) {
                $cur_users_count = $segment_service->getSegmentProvisionUsersCountByDate($sp->id);
                $prev_users_count = $segment_service->getSegmentProvisionUsersCountByDate($sp->id, $previous_date);
                $segment_status = $segment_service->getUsersCountStatus($cur_users_count, $prev_users_count);

                $total_users_count += $cur_users_count->total;
                $total_prev_users_count += $prev_users_count->total;

                if ($segment_status == SegmentProvisionUsersCount::USERS_COUNT_STATUS_UP) {
                    $params['sp_data'][$sp->id]['diff'] = $cur_users_count->total - $prev_users_count->total;
                }

                $params['sp_data'][$sp->id]['status'] = $segment_status;
                $params['sp_data'][$sp->id]['counter_text'] = SegmentProvisionUsersCount::getUserCounterText($cur_users_count->total, $prev_users_count->total);
                $params['sp_data'][$sp->id]['condition_brief_text'] = $create_sql_service->getConditionsBriefText(json_decode($sp->provision, true));
            }

            $segment_status = $segment_service->getUserCountTotalStatus($total_users_count, $total_prev_users_count);
            if ($segment_status == SegmentProvisionUsersCount::USERS_COUNT_STATUS_UP) {
                $params['sp_data']['total']['diff'] = $total_users_count - $total_prev_users_count;
            }

            $params['sp_data']['total']['status'] = $segment_status;
            $params['sp_data']['total']['counter_text'] = SegmentProvisionUsersCount::getUserCounterText($total_users_count, $total_users_count);
        } else {
            foreach ($params['segment_provisions'] as $sp) {
                $params['sp_data'][$sp->id]['condition_brief_text'] = $create_sql_service->getConditionsBriefText(json_decode($sp->provision, true));
            }
        }

        return $params;
    }

    private function getSegmentUrl($segment) {

        $action = $segment->isSegmentGroup() ? 'segment_group' : 'conditional_segment';
        
        return Util::rewriteUrl('admin-segment', $action, array($segment->id));
    }
}
