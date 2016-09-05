<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class SegmentProvisionUsersCount extends aafwEntityBase {

    const USERS_COUNT_PROCESSING        = 0;
    const USERS_COUNT_STATUS_UNCHANGED  = 1;
    const USERS_COUNT_STATUS_UP         = 2;

    /**
     * @param $cur_count
     * @param $prev_count
     * @return string
     */
    public static function getUserCounterText($cur_count, $prev_count) {
        if ($cur_count) {
            return $cur_count;
        }

        if ($cur_count !== 0 && $prev_count) {
            return $prev_count;
        }

        return "-";
    }
}