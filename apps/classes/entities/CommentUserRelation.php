<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CommentUserRelation extends aafwEntityBase {

    const ANONYMOUS_FLG_OFF = "0";
    const ANONYMOUS_FLG_ON  = "1";

    const DISCARD_FLG_OFF   = "0";
    const DISCARD_FLG_ON    = "1";

    const OBJECT_TYPE_COMMENT  = 1;
    const OBJECT_TYPE_REPLY    = 2;

    const COMMENT_USER_RELATION_STATUS_ALL      = "0";
    const COMMENT_USER_RELATION_STATUS_APPROVED = "1";
    const COMMENT_USER_RELATION_STATUS_REJECTED = "2";

    const NOTE_STATUS_ALL       = "0";
    const NOTE_STATUS_VALID     = "1";
    const NOTE_STATUS_INVALID   = "2";

    const SNS_SHARE_ALL     = "0";
    const USE_SNS_SHARE     = "1";
    const NOT_USE_SNS_SHARE = "2";

    public static $comment_user_relation_status_options = array(
        self::COMMENT_USER_RELATION_STATUS_ALL      => '全て',
        self::COMMENT_USER_RELATION_STATUS_APPROVED => '公開',
        self::COMMENT_USER_RELATION_STATUS_REJECTED => '非公開'
    );

    public static $comment_user_relation_statuses = array(
        self::COMMENT_USER_RELATION_STATUS_APPROVED => '公開',
        self::COMMENT_USER_RELATION_STATUS_REJECTED => '非公開'
    );

    public static $comment_use_relation_order_kinds = array(
        1 => 'created_at',
        2 => 'user_id'
    );

    public static $comment_use_relation_order_kind_label = array(
        1 => '投稿順',
        2 => 'ユーザーID順'
    );

    public static $comment_use_relation_order_types = array(
        1 => 'desc',
        2 => 'asc'
    );

    public static $comment_use_relation_order_type_label = array(
        1 => '[Z-A↑] 降順',
        2 => '[A-Z↓] 昇順'
    );

    public static $note_status_options = array(
        self::NOTE_STATUS_ALL       => '全て',
        self::NOTE_STATUS_VALID     => 'あり',
        self::NOTE_STATUS_INVALID   => 'なし'
    );

    public static $sns_share_options = array(
        self::SNS_SHARE_ALL     => '全て',
        self::USE_SNS_SHARE     => 'あり',
        self::NOT_USE_SNS_SHARE => 'なし'
    );

    /**
     * 非公開の投稿であるかどうか
     * @return bool
     */
    public function isRejected() {
        if ($this->status != self::COMMENT_USER_RELATION_STATUS_REJECTED) {
            return false;
        }

        return true;
    }

    /**
     * ユーザーによって削除されたかどうか
     * @return bool
     */
    public function isDiscard() {
        if ($this->discard_flg == self::DISCARD_FLG_OFF) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isAnonymousUser() {
        if ($this->anonymous_flg == self::ANONYMOUS_FLG_OFF) {
            return false;
        }

        return true;
    }
}
