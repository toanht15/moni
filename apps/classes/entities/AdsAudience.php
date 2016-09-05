<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class AdsAudience extends aafwEntityBase {

    const STATUS_DRAFT = 0;
    const STATUS_ACTIVE = 1;

    const DESCRIPTION_FLG_OFF = 0;
    const DESCRIPTION_FLG_ON = 1;

    const SEACH_TYPE_ADS = 0;
    const SEACH_TYPE_SEGMENT = 1;

    public function isActiveAudience() {
        return $this->status == self::STATUS_ACTIVE;
    }
}
