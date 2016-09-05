<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.BrandsUsersRelationService');
AAFW::import('jp.aainc.classes.services.UserPublicProfileInfoService');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class UserService extends aafwServiceBase {
	protected $user;
    private static $_instance = null;

	public function __construct() {
		$this->user = $this->getModel("Users");
	}

	public function getUserByMoniplaUserId($moniplaUserId) {
		$filter = array(
			'monipla_user_id' => $moniplaUserId,
		);
		return $this->user->findOne($filter);
	}
    
    public function getUsersByMoniplaUserIds($moniplaUserIds) {
        $filter = array(
            'monipla_user_id' => $moniplaUserIds,
        );
        return $this->user->find($filter);
    }

    public function getUserByMailAddress($mailAddress) {
        $filter = array(
            'mail_address' => $mailAddress,
        );
        return $this->user->findOne($filter);
    }

    public function getUsersByMailAddress($mailAddress) {
        $filter = array(
            'mail_address' => $mailAddress,
        );
        return $this->user->find($filter);
    }

    public function updateUser($user) {
        return $this->user->save($user);
    }

	public function createUser($user) {
		$this->user->save($user);
	}

	public function createEmptyUser() {
		return $this->user->createEmptyObject();
	}

    /**
     * 各ブランド毎に管理者フラグを所持するユーザ情報を取得
     * @param $brandId
     */
    public function getAdminUsers($brandId) {
        $users_relation_service = new BrandsUsersRelationService();
        $brands_admin_users = $users_relation_service->getBrandsAdminUsersByBrandId($brandId);
        $admin_user = array();

        foreach($brands_admin_users as $brands_admin_user) {
            $admin_user[] = $brands_admin_user->getUser();
        }

        return $admin_user;
    }

    public function getAdminUserAll(){
        $brands_users_relation_service = new BrandsUsersRelationService();
        return $brands_users_relation_service->getAdminUsers();
    }

    public function getUserByBrandcoUserId($UserId) {
        $filter = array(
            'id' => $UserId,
        );
        return $this->user->findOne($filter);
    }

    /**
     * @param $user_ids
     * @return mixed
     */
    public function getUserByBrandcoUserIds($user_ids) {
        $filter = array(
            'id' => $user_ids
        );

        return $this->user->find($filter);
    }

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * @param $email
     * @return mixed
     */
    public function getUserByEmail($email) {
        $filter = array(
            'conditions' => array(
                'mail_address' => $email
            )
        );
        return $this->user->findOne($filter);
    }

    /**
     * @param $brand_id
     * @param $user_id
     * @return int|mixed
     */
    public function countUnreadMessages($brand_id, $user_id) {
        $data_builder = new aafwDataBuilder();
        $result = $data_builder->countUnreadMessage(array("BRAND_ID" => $brand_id, "USER_ID" => $user_id));
        return $result[0]['COUNT(*)'];
    }

    /**
     * @param $brand_id
     * @param $user_id
     * @return int|mixed|null
     */
    public function getUnreadMessagesCount($brand_id, $user_id) {
        $cache_manager = new CacheManager();
        $notification_count = $cache_manager->getNotificationCount($brand_id, $user_id);

        //念のため1日で初回ログインするときにnotificationを更新する
        if (!is_array($notification_count) || ($notification_count[CacheManager::JSON_KEY_UPDATED_AT] != date('Y-m-d')) || is_null($notification_count[CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT])) {
            $notification_count[CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT] = $this->countUnreadMessages($brand_id, $user_id);
            $cache_manager->setNotificationCount(
                array(CacheManager::JSON_KEY_UPDATED_AT => date('Y-m-d'),
                    CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT => $notification_count[CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT]),
                $brand_id, $user_id);
        }

        return $notification_count[CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT];
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getUserPublicInfo($user) {
        $public_profile_info_service = new UserPublicProfileInfoService();

        $public_profile_info = $public_profile_info_service->getPublicProfileInfo($user->id);

        if (!Util::isNullOrEmpty($public_profile_info) && !Util::isNullOrEmpty($public_profile_info->nickname)) {
            $user->name = $public_profile_info->nickname;
        }

        if (Util::isNullOrEmpty($user->profile_image_url)) {
            $php_parser = new PHPParser();
            $user->profile_image_url = $php_parser->setVersion('/img/base/imgUser1.jpg');
        }

        return $user;
    }

    /**
     * @param $monipla_user_id
     * @return mixed
     */
    public function getUserPublicInfoByMoniplaUserId($monipla_user_id) {
        $user = $this->getUserByMoniplaUserId($monipla_user_id);

        return $this->getUserPublicInfo($user);
    }

    /**
     * @param $brandco_user_id
     * @return mixed
     */
    public function getUserPublicInfoByBrandcoUserId($brandco_user_id) {
        $user = $this->getUserByBrandcoUserId($brandco_user_id);

        return $this->getUserPublicInfo($user);
    }
}