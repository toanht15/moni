<?php

AAFW::import('jp.aainc.classes.validator.BaseValidator');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');

class UserCampaignJoinValidator extends BaseValidator {

    private $cp;
    private $service_factory;
    const CP_NOT_EXIST = "キャンペーンが存在しません";
    const CP_CANT_JOIN = "キャンペーンに参加出来ません";

    public function __construct($cp) {
        parent::__construct();
        $this->cp = $cp;
        $this->service_factory = new aafwServiceFactory();
    }

    public function validate() {

        // $cp_idのチェック
        if (trim($this->cp->id) === '') {
            $this->errors['cp_id'][] = self::CP_NOT_EXIST;
            return;
        }

        /** @var Cp $cp */
        $cp = $this->cp;

        // キャンペーンの存在チェック
        if (!$cp->id) {
            $this->errors['cp_id'][] = self::CP_NOT_EXIST;
            return;
        }

        // キャンペーンのステータスチェック
        if ($cp->status != Cp::STATUS_FIX && $cp->status != Cp::STATUS_DEMO) {
            $this->errors['cp_id'][] = self::CP_NOT_EXIST;
            return;
        }

        // キャンペーンの当選者数チェック
        if ($cp->isOverLimitWinner()) {
            if ($cp->selection_method == CpCreator::ANNOUNCE_FIRST) {
                $this->errors['cp_id'][] = config("@message.userMessage.cp_join_limit.msg");
            } else if ($cp->selection_method == CpCreator::ANNOUNCE_LOTTERY) {
                $this->errors['cp_id'][] = config("@message.userMessage.cp_winner_limit.msg");
            }
            return;
        }

        $status = RequestuserInfoContainer::getInstance()->getStatusByCp($cp);
        if ($status != Cp::CAMPAIGN_STATUS_OPEN && $status != Cp::CAMPAIGN_STATUS_DEMO) {
            $this->errors['cp_id'][] = self::CP_NOT_EXIST;
            return;
        }

        // キャンペーンに応募できるかチェック
        if ($cp->isOverTime()) {
            $this->errors['cp_id'][] = self::CP_CANT_JOIN;
        }
    }
} 