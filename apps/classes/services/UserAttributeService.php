<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.UserService');
AAFW::import('jp.aainc.classes.core.UserAttributeManager');

class UserAttributeService extends aafwServiceBase {
    protected $user_attribute;
    private $userService;
    private static $_instance = null;

    const ATTRIBUTE_SEX_MAN = 'm';
    const ATTRIBUTE_SEX_WOMAN = 'f';
    const ATTRIBUTE_SEX_UNKWOWN = 'n'; //実際はデータは存在しないが、ファン一覧の絞り込み時に使用される

    public function __construct() {
        $this->user_attribute = $this->getModel("UserAttributes");
        if (!$this->userService) $this->userService = UserService::getInstance();
    }

    public function getUserAttribute($userId, $userAttributeMasterId) {

        $conditions = array(
            'user_id' => $userId,
            'user_attribute_master_id' => $userAttributeMasterId,
        );

        return $this->user_attribute->findOne($conditions);
    }

    public function addUserAttributes($monipla_user_id, $key, $json_value) {
        $user = $this->userService->getUserByMoniplaUserId($monipla_user_id);
        $user_attribute = $this->getUserAttribute($user->id, $key);

        if(!$user_attribute) {
            $user_attribute = $this->createEmptyUserAttribute();
            $user_attribute->user_id = $user->id;
            $user_attribute->user_attribute_master_id = $key;
        }
        $user_attribute->value = $json_value;
        $this->createUserAttribute($user_attribute);
    }

    public function createUserAttribute($user_attribute) {
        if (is_null($user_attribute->value) || $user_attribute->value === 'null') {
            aafwLog4phpLogger::getHipChatLogger()->error("UserAttributeService#createUserAttribute value is null. user_id = " . $user_attribute->user_id);
            aafwLog4phpLogger::getHipChatLogger()->error(json_encode(debug_backtrace()));
        }

        $this->user_attribute->save($user_attribute);
    }

    public function createEmptyUserAttribute() {
        return $this->user_attribute->createEmptyObject();
    }

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

}