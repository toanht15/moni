<?php

AAFW::import('jp.aainc.classes.validator.BaseValidator');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class PreJoinActionValidator extends BaseValidator {

    private $cp_user_id;
    private $cp_action_id;
    private $service_factory;
    private $cp_user;

    public function __construct($cp_user_id, $cp_action_id, $cp_user = null) {
        $this->cp_user_id = $cp_user_id;
        $this->cp_action_id = $cp_action_id;
        $this->service_factory = new aafwServiceFactory();
        $this->cp_user = $cp_user;
    }

    public function validate() {

        // $cp_user_idのチェック
        if (trim($this->cp_user_id) === '') {
            $this->errors['cp_user_id'][] = "キャンペーンユーザーが存在しません";
            return;
        }

        // $cp_action_idのチェック
        if (trim($this->cp_action_id) === '') {
            $this->errors['cp_action_id'][] = "アクションが存在しません";
            return;
        }

        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->service_factory->create('CpUserService');

        $cp_user = $this->cp_user;
        if ($cp_user === null) {
            /** @var CpUser $cp_user */
            $cp_user = $cp_user_service->getCpUserById($this->cp_user_id);
        }

        // キャンペーンユーザーの存在チェック
        if (!$cp_user->id) {
            $this->errors['cp_user_id'][] = "キャンペーンユーザーが存在しません";
            return;
        }

        /** @var Cp $cp */
        $cp = CpInfoContainer::getInstance()->getCpById($cp_user->cp_id);

        // キャンペーンの存在チェック
        if (!$cp->id) {
            $this->errors['cp_id'][] = "キャンペーンが存在しません";
            return;
        }

        /** @var CpUserActionStatus $cp_user_action_status */
        $cp_user_action_status = $cp_user_service->getCpUserActionStatus($this->cp_user_id, $this->cp_action_id);

        // アクションステータスの存在チェック
        if (!$cp_user_action_status->id) {
            $this->errors['cp_action_id'][] = "cp_action_idが存在しません";
            return;
        }
    }
}
