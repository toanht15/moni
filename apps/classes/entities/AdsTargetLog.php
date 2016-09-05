<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class AdsTargetLog extends aafwEntityBase {

    const SEND_TARGET_FAIL = 0;
    const SEND_TARGET_SUCCESS = 1;

    public function isSendTargetSuccess() {
        return $this->status == self::SEND_TARGET_SUCCESS;
    }
}
