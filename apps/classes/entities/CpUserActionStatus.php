<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpUserActionStatus extends aafwEntityBase {

    const NOT_JOIN = 0;
    const JOIN = 1;
    const CAN_NOT_JOIN = 2;

    const CELL_STATUS_STANDARD = 0;
    const CELL_STATUS_PHOTO = 1;
    const CELL_STATUS_INSTANT_WIN = 2;
    const CELL_STATUS_COUPON = 3;
    const CELL_STATUS_FREE_ANSWER = 4;

    const STATUS_FINISH = '完了';
    const STATUS_UNSENT = '';
    const STATUS_UNSENT_STR = '未送信';
    const STATUS_UNREAD = '未読';
    const STATUS_READ = '既読';
    const STATUS_WIN = '当選';
    const STATUS_LOSE = '落選';
    const STATUS_COUNT_INSTANT_WIN = '抽選回数指定';
    const STATUS_REJECTED = '参加条件外';
    const STATUS_ANNOUNCE_DELIVERED = '確定';

    //当選状態
    const STATUS_NOT_WIN = '未当選';

    const DEVICE_TYPE_OTHERS = 0;
    const DEVICE_TYPE_SP = 1;

    protected $_Relations = array(

        'CpUsers' => array(
            'cp_user_id' => 'id',
        ),

        'CpActions' => array(
            'cp_action_id' => 'id',
        ),
    );

    public function isJoin() {
        return $this->status == self::JOIN;
    }

    public function isNotJoin() {
        return $this->status == self::NOT_JOIN;
    }
}
