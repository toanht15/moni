<?php

AAFW::import('jp.aainc.classes.validator.BaseValidator');

class UserMessagesThreadValidator extends BaseValidator {

    private $cp_id;
    private $brand_id;
    private $service_factory;
    private $cp;
    private $cp_user;

    public function __construct($cp_id, $user_id, $brand_id, $cp, $cp_user) {
        parent::__construct();
        $this->cp_id = $cp_id;
        $this->brand_id = $brand_id;
        $this->user_id = $user_id;
        $this->cp = $cp;
        $this->cp_user = $cp_user;
        $this->service_factory = new aafwServiceFactory();
    }

    public function validate() {

        // $cp_idのチェック
        if (trim($this->cp_id) === '') {
            $this->errors['cp_id'][] = "キャンペーンが存在しません";
            return;
        }

        // $user_idのチェック
        if (trim($this->user_id) === '') {
            $this->errors['user_id'][] = "ユーザーが存在しません";
            return;
        }

        /** @var Cp $cp */
        $cp = $this->cp;

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

        // キャンペーンユーザーの存在チェック
        if (!$cp_user_service->isJoinedCp($this->cp_id, $this->user_id, $this->cp_user, $this->cp)) {
            $this->errors['cp_user_id'][] = "キャンペーンユーザーが存在しません";
        }
    }
} 