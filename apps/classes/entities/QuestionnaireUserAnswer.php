<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class QuestionnaireUserAnswer extends aafwEntityBase {

    const APPROVAL_STATUS_APPROVE       = 1;
    const APPROVAL_STATUS_REJECT        = 2;
    const APPROVAL_STATUS_UNAPPROVED    = 3;

    private static $approval_status_classes = array(
        self::APPROVAL_STATUS_APPROVE       => 'label4',
        self::APPROVAL_STATUS_REJECT        => 'label2',
        self::APPROVAL_STATUS_UNAPPROVED    => 'label5'
    );

    private static $approval_statuses = array(
        self::APPROVAL_STATUS_APPROVE       => '承認',
        self::APPROVAL_STATUS_REJECT        => '非承認',
        self::APPROVAL_STATUS_UNAPPROVED    => '未承認'
    );

    protected $_Relations = array(
        'CpActions' => array(
            'cp_action_id' => 'id',
        ),
        'BrandsUsersRelations' => array(
            'brands_users_relation_id' => 'id'
        )
    );

    public static function getApprovalStatusClass($approval_status) {
        return self::$approval_status_classes[$approval_status];
    }

    public static function getApprovalStatus($approval_status) {
        return self::$approval_statuses[$approval_status];
    }
}
