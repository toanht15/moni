<?php
/**
 * User: t-yokoyama
 * Date: 15/03/10
 * Time: 13:32
 */

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpTwitterFollowLog extends aafwEntityBase {

    const STATUS_ACTION_UNREAD  = '非表示';
    const STATUS_ACTION_EXEC    = '新規フォロー';
    const STATUS_ACTION_ALREADY = '既存フォロー';
    const STATUS_ACTION_SKIP    = 'スキップ';

    public static $tw_follow_statuses = array(
        CpTwitterFollowActionManager::FOLLOW_ACTION_UNREAD    => self::STATUS_ACTION_UNREAD,
        CpTwitterFollowActionManager::FOLLOW_ACTION_ALREADY   => self::STATUS_ACTION_ALREADY,
        CpTwitterFollowActionManager::FOLLOW_ACTION_EXEC      => self::STATUS_ACTION_EXEC,
        CpTwitterFollowActionManager::FOLLOW_ACTION_SKIP      => self::STATUS_ACTION_SKIP
    );

    protected $_Relations = array(
        'CpActions' => array(
            'cp_action_id' => 'id',
        ),
        'CpUsers' => array(
            'cp_user_id' => 'id',
        ),
    );

    public function isStatusUnread() {
        return $this->status == CpTwitterFollowActionManager::FOLLOW_ACTION_UNREAD ?: false;
    }
}
