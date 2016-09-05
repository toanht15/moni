<?php

AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
AAFW::import('jp.aainc.classes.brandco.cp.CpFacebookLikeActionManager');

/**
 * @property mixed directory_name
 */
class CpFacebookLikeLog extends aafwEntityBase {

    const LIKE_ACTION_UNREAD    = '0';    // Facebook未連携
    const LIKE_ACTION_EXEC      = '1';    // いいねしていない
    const LIKE_ACTION_ALREADY   = '2';    // 過去にいいね済み
    const LIKE_ACTION_SKIP      = '3';    // いいねスキップ
    const LIKE_ACTION_CLOSE     = '4';    // いいねモジュール実行済み

    const STATUS_ACTION_UNREAD  = '非表示';
    const STATUS_ACTION_EXEC    = '新規いいね！';
    const STATUS_ACTION_ALREADY = '既存いいね！';
    const STATUS_ACTION_SKIP    = 'スキップ';

    public static $fb_like_statuses = array(
        self::LIKE_ACTION_UNREAD    => self::STATUS_ACTION_UNREAD,
        self::LIKE_ACTION_ALREADY   => self::STATUS_ACTION_ALREADY,
        self::LIKE_ACTION_EXEC      => self::STATUS_ACTION_EXEC,
        self::LIKE_ACTION_SKIP      => self::STATUS_ACTION_SKIP
    );

    public function isStatusExec() {
        return $this->status == self::LIKE_ACTION_EXEC ?: false;
    }

    public function isStatusAlready() {
        return $this->status == self::LIKE_ACTION_ALREADY ?: false;
    }

    public function isStatusSkip() {
        return $this->status == self::LIKE_ACTION_SKIP ?: false;
    }
}
