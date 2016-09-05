<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class InstagramHashtagUserPost extends aafwEntityBase {

    protected $_Relations = array(
        'InstagramHashtagUsers' => array(
            'instagram_hashtag_user_id' => 'id'
        ),
        'CpInstagramHashtagEntries' => array(
            'id' => 'instagram_hashtag_user_post_id'
        )
    );

    const APPROVAL_STATUS_DEFAULT   = 0;
    const APPROVAL_STATUS_APPROVE   = 1;
    const APPROVAL_STATUS_REJECT    = 2;
    const APPROVAL_STATUS_PRIVATE   = 3;

    const PERPAGE_CP_EVERYONE_POST_PC = 8;
    const PERPAGE_CP_EVERYONE_POST_SP = 6;

    const REVERSE_POST_TIME_DEFAULT = 0;
    const REVERSE_POST_TIME_INVALID = 1;

    private $approval_status_classes = array(
        self::APPROVAL_STATUS_DEFAULT => 'label5',
        self::APPROVAL_STATUS_APPROVE => 'label4',
        self::APPROVAL_STATUS_REJECT => 'label2',
        self::APPROVAL_STATUS_PRIVATE => 'label7',
    );

    private $approval_statuses = array(
        self::APPROVAL_STATUS_DEFAULT => '未承認',
        self::APPROVAL_STATUS_APPROVE => '承認',
        self::APPROVAL_STATUS_REJECT => '非承認',
        self::APPROVAL_STATUS_PRIVATE => '削除済'
    );

    private $reverse_post_time_statuses = array(
        self::REVERSE_POST_TIME_DEFAULT => '登録後投稿',
        self::REVERSE_POST_TIME_INVALID => '投稿後登録'
    );

    public function getApprovalStatusClass() {
        return $this->approval_status_classes[$this->approval_status];
    }

    public function getApprovalStatus() {
        return $this->approval_statuses[$this->approval_status];
    }

    public function getReversePostTimeStatus() {
        return $this->reverse_post_time_statuses[$this->reverse_post_time_flg];
    }
}
