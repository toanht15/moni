<?php

/**
 * Class SegmentActionTrait
 *
 * Segment Actionの共有関数はここでまとめる
 *
 */
trait SegmentActionTrait {

    public function saveSegmentActionLog($type) {
        /** @var SegmentService $segment_service */
        $segment_service = $this->createService('SegmentService');

        /** @var SegmentActionLogService $segment_action_log_service */
        $segment_action_log_service = $this->createService('SegmentActionLogService');

        $brand = $this->getBrand();

        $provision_ids = $segment_service->getProvisionIdsFromSession($this->sp_ids_array);

        $previous_date = strtotime('yesterday');
        $cur_date = strtotime('today');

        $created_dates = array($previous_date, $cur_date);

        $target_user_count = $segment_service->calculateSegmentProvisionUserCount($provision_ids, $created_dates);

        $log_data = array(
            'brand_id' => $brand->id,
            'segment_provison_ids' => json_encode($this->sp_ids_array),
            'type' => $type,
            'total' => $target_user_count
        );

        return $segment_action_log_service->createSegmentActionLog($log_data);
    }

    public function isContainInvalidSegment($segment_ids, $brand_id) {

        foreach ($segment_ids as $segment_id) {

            $segment_validator = new SegmentValidator($segment_id, $brand_id);
            $segment_validator->validate();

            if (!$segment_validator->isValid()) {
                return true;
            }

            if(!$segment_validator->getCurSegment()->isActive()) {
                return true;
            }
        }

        return false;
    }

}