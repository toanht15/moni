<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.UserService');
AAFW::import('jp.aainc.classes.entities.BrandPageSetting');

class UserSearchInfoService extends aafwServiceBase {

    protected $user_search_info;
    protected $user_search_info_data;
    private static $_instance = null;

    public function __construct() {
        $this->user_search_info = $this->getModel("UserSearchInfos");
        if (!$this->userService) $this->userService = UserService::getInstance();
    }

    public function getUserSearchInfo($userId) {
        $conditions = array(
            'conditions' => array(
                'user_id' => $userId
            )
        );

        return $this->user_search_info->findOne($conditions);
    }

    public function addUserAttributes($monipla_user_id, $key, $value, $page_setting) {
        $user = $this->userService->getUserByMoniplaUserId($monipla_user_id);
        $this->setUserAttribute($user->id, $key, $value, $page_setting);
    }

    /**
     * ユーザ属性情報の保存処理を行う
     * @param $user_id
     * @param $key
     * @param $value
     * @param $page_setting
     */
    public function setUserAttribute($user_id, $key, $value, $page_setting) {
        if(!$this->user_search_info_data) {
            $this->user_search_info_data = $this->getUserSearchInfo($user_id);
        }
        if(!$this->user_search_info_data) {
            $this->user_search_info_data = $this->createEmptyUserSearchInfo();
            $this->user_search_info_data->user_id = $user_id;
        }
        if($key == UserAttributeManager::SEX) {
            $this->user_search_info_data->sex = $value;
            // 生年月日の登録がある場合はここではsaveしない
            if($page_setting && !$page_setting->privacy_required_birthday) {
                $this->createUserSearchInfo($this->user_search_info_data);
            }
        }
        if($key == UserAttributeManager::BIRTH_DAY) {
            $this->user_search_info_data->birthday = $value;
            $this->createUserSearchInfo($this->user_search_info_data);
        }
    }

    public function createUserSearchInfo($user_search_info) {
        $this->user_search_info->save($user_search_info);
    }

    public function createEmptyUserSearchInfo() {
        return $this->user_search_info->createEmptyObject();
    }

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

}
