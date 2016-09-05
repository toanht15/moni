<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );

class AdsAudiencesAccountsRelation extends aafwEntityBase {

    const SEND_MIXED_TYPE = 0;
    const SEND_MAIL_TYPE = 1;
    const SEND_ID_TYPE = 2;
    
    const AUTO_SEND_TARGET_FLG_OFF = 0;
    const AUTO_SEND_TARGET_FLG_ON = 1;

    public function isAutoSendTarget() {
        return $this->auto_send_target_flg == self::AUTO_SEND_TARGET_FLG_ON;
    }
}
