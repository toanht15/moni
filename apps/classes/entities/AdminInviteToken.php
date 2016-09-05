<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class AdminInviteToken extends aafwEntityBase {

    public function canUse() {

        return $this->used_flg == 0;
    }
}
