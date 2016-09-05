<?php

AAFW::import('jp.aainc.classes.validator.BaseValidator');

class CampaignPageValidator extends BaseValidator {

    private $cp_id;
    private $brand_id;
    private $service_factory;
    private $user_info;
    private $demo_token;
    private $cp;

    public function __construct($cp_id, $user_info = null, $brand_id = null, $cp = null) {
        parent::__construct();
        $this->cp_id = $cp_id;
        $this->brand_id = $brand_id;
        $this->user_info = $user_info;
        $this->service_factory = new aafwServiceFactory();
        $this->cp = $cp;
    }

    public function setDemoToken($token) {
        $this->demo_token = $token;
    }

    public function validate() {

        // $cp_idのチェック
        if (trim($this->cp_id) === '') {
            $this->errors['cp_id'][] = "キャンペーンが存在しません";
            return;
        }

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->service_factory->create('CpFlowService');

        $cp = $this->cp;
        if ($cp === null) {
            /** @var Cp $cp */
            $cp = $cp_flow_service->getCpById($this->cp_id);
        }

        // キャンペーンの存在チェック
        if (!$cp->id) {
            $this->errors['cp_id'][] = "キャンペーンが存在しません";
            return;
        }

        // 不正ブランド
        if ($cp->brand_id != $this->brand_id) {
            $this->errors['cp_id'][] = "キャンペーンが存在しません";
            return;
        }

        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->service_factory->create('CpUserService');
        if ($this->user_info->id) {
            $cpUser = $cp_user_service->getCpUserByCpIdAndUserId($cp->id, $this->user_info->id);
        }

        // キャンペーンのステータスチェック
        if ($cp->status != Cp::STATUS_FIX && $cp->status != Cp::STATUS_CLOSE &&
            !($cp->status == Cp::STATUS_DEMO && ($this->demo_token == hash("sha256", $cp->created_at) || $cpUser))) {

            $this->errors['cp_id'][] = "キャンペーンが存在しません";
            return;
        }

        // キャンペーンのタイプチェック
        if ($cp->type != Cp::TYPE_CAMPAIGN) {
            $this->errors['cp_id'][] = "キャンペーンが存在しません";
            return;
        }

        if($cp->join_limit_flg == cp::JOIN_LIMIT_ON) {
            if(!$this->user_info) {
                $this->errors['cp_id'][] = "キャンペーンが存在しません";
                return;
            }
            if(!$cpUser) {
                $this->errors['cp_id'][] = "キャンペーンが存在しません";
                return;
            }
        }
    }
}
