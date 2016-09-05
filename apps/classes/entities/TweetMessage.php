<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class TweetMessage extends aafwEntityBase {
    const TWEET_ACTION_EXEC     = 0;
    const TWEET_ACTION_SKIP     = 1;

    const APPROVAL_STATUS_APPROVE   = 1;
    const APPROVAL_STATUS_REJECT    = 2;

    const TWEET_STATUS_PUBLIC       = 0;
    const TWEET_STATUS_PRIVATE      = 1;
    const TWEET_STATUS_REMOVED      = 2;

    const STATUS_ACTION_EXEC    = 'ツイート';
    const STATUS_ACTION_SKIP    = 'スキップ';

    private $approval_status_classes = array(
        self::APPROVAL_STATUS_APPROVE => 'label4',
        self::APPROVAL_STATUS_REJECT => 'label2'
    );

    private static $approval_statuses = array(
        self::APPROVAL_STATUS_APPROVE => '出力',
        self::APPROVAL_STATUS_REJECT => '非出力'
    );

    private $tweet_status_classes = array(
        self::TWEET_STATUS_PUBLIC => 'label3',
        self::TWEET_STATUS_PRIVATE => 'label8',
        self::TWEET_STATUS_REMOVED => 'label7'
    );

    private static $tweet_statuses_text = array(
        self::TWEET_STATUS_PUBLIC => '公開',
        self::TWEET_STATUS_PRIVATE => '非公開',
        self::TWEET_STATUS_REMOVED => '削除済'
    );

    public static $tweet_statuses = array(
        self::TWEET_ACTION_EXEC => self::STATUS_ACTION_EXEC,
        self::TWEET_ACTION_SKIP => self::STATUS_ACTION_SKIP
    );

    protected $_Relations = array(
        'CpUsers' => array(
            'cp_user_id' => 'id',
        ),
        'CpTweetActions' => array(
            'cp_tweet_action_id' => 'id',
        )
    );

    public function getApprovalStatusClass() {
        return $this->approval_status_classes[$this->approval_status];
    }

    public function getApprovalStatus() {
        return self::$approval_statuses[$this->approval_status];
    }

    public static function getStaticApprovalStatus($approval_status) {
        return self::$approval_statuses[$approval_status];
    }

    public function getTweetStatusClass() {
        return $this->tweet_status_classes[$this->tweet_status];
    }

    public function getTweetStatus() {
        return self::$tweet_statuses_text[$this->tweet_status];
    }

    public static function getStaticTweetStatus($static_status) {
        return self::$tweet_statuses_text[$static_status];
    }
}
