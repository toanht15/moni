<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
AAFW::import('jp.aainc.classes.services.monipla.OldMoniplaUserOptinService');

class ManagerUserSearchService extends aafwServiceBase {

    const SEARCH_TYPE_PL_UID = 1;
    const SEARCH_TYPE_BRC_UID = 2;
    const SEARCH_TYPE_SNS_UID = 3;
    const SEARCH_TYPE_PL_MAIL = 4;
    const SEARCH_TYPE_BRC_MAIL = 5;
    const SEARCH_TYPE_BRC_NO = 6;

    const PLATFORM_USER_OPTIN = 1;
    const PLATFORM_USER_OPTOUT = 0;

    public static $managerUserSearchType = array(
        self::SEARCH_TYPE_PL_UID => 'Platform ID',
        self::SEARCH_TYPE_BRC_UID => 'BRANDCo UID',
        self::SEARCH_TYPE_SNS_UID => 'SNS UID',
        self::SEARCH_TYPE_PL_MAIL => 'AlliedID メールアドレス',
        self::SEARCH_TYPE_BRC_MAIL => 'BRNADCo メールアドレス',
        self::SEARCH_TYPE_BRC_NO => 'ブランド会員番号'
    );

    protected $db;
    protected $moniplaCore;

    /** @var UserService $user_service */
    protected $user_service;
    /** @var BrandsUsersRelationService $brands_users_relation_service */
    protected $brands_users_relation_service;
    /** @var SocialAccountService $social_account_service */
    protected $social_account_service;

    function __construct() {
        $this->db = aafwDataBuilder::newBuilder();
        $this->user_service = $this->getService('UserService');
        $this->brands_users_relation_service = $this->getService('BrandsUsersRelationService');
        $this->social_account_service = $this->getService('SocialAccountService');
    }

    /**
     * 検索条件からplatform_user_idを検索し、idを配列で返す
     * @param $search_type
     * @param $get_params
     * @return mixed
     */
    public function searchPlatformUserIds($search_type, $get_params) {
        if (!$search_type || !$get_params) return;

        $ids = array();
        switch ($search_type) {
            case self::SEARCH_TYPE_PL_UID:
                $id = $this->getPlatformUser($get_params['platform_user_id'])->id;
                $ids[] = $id >= 1 ? $id : null;
                break;
            case self::SEARCH_TYPE_BRC_UID:
                $ids[] = $this->user_service->getUserByBrandcoUserId($get_params['brandco_user_id'])->monipla_user_id;
                break;
            case self::SEARCH_TYPE_SNS_UID:
                $ids[] = $this->searchPlatformUserIdBySnsUserId($get_params['social_media_id'], $get_params['social_media_account_id']);
                break;
            case self::SEARCH_TYPE_PL_MAIL:
                $id = $this->getUserByMailAddress($get_params['platform_mail_address'])->id;
                $ids[] = $id >= 1 ? $id : null;
                break;
            case self::SEARCH_TYPE_BRC_MAIL:
                $ids[] = $this->user_service->getUserByMailAddress($get_params['brandco_mail_address'])->monipla_user_id;
                break;
            case self::SEARCH_TYPE_BRC_NO:
                $ids[] = $this->searchPlatformUserIdByBrandNo($get_params['brand_id'], $get_params['brand_user_no']);
                break;
        }
        return $ids;
    }

    /**
     * SNSのタイプとaccount_idからplatform_user_idを検索
     * @param $social_media_id
     * @param $social_media_account_id
     * @return mixed
     */
    public function searchPlatformUserIdBySnsUserId($social_media_id, $social_media_account_id) {
        if (!$social_media_id || !$social_media_account_id) return;

        $social_account = $this->social_account_service->getSocialAccountBySocialMediaIdAndSocialMediaAccountId($social_media_id, $social_media_account_id);
        return $this->user_service->getUserByBrandcoUserId($social_account->user_id)->monipla_user_id;
    }

    /**
     * BRANDCoのbrand_idと会員番号からplatform_user_idを検索
     * @param $brand_id
     * @param $no
     * @return entity
     */
    public function searchPlatformUserIdByBrandNo($brand_id, $no) {
        if (!$brand_id || !$no) return;

        $relation = $this->brands_users_relation_service->getBrandsUsersRelationByBrandIdAndNo($brand_id, $no);
        return $this->user_service->getUserByBrandcoUserId($relation->user_id)->monipla_user_id;
    }

    /**
     * Platformのメールアドレスからplatform_user_infoを取得
     * @param $mail_address
     * @return mixed
     */
    public function getUserByMailAddress($mail_address) {
        if (!$mail_address) return;

        return $this->getMoniplaCore()->getUserByQuery(array(
            'class' => 'Thrift_UserQuery',
            'fields' => array(
                'mailAddress' => $mail_address
            )
        ));
    }

    /**
     * Platform_user_idからplatform_user_infoを取得
     * @param $platform_user_id
     * @return mixed
     */
    public function getPlatformUser($platform_user_id) {
        if (!$platform_user_id) return;

        return $this->getMoniplaCore()->getUserByQuery(array(
            'class' => 'Thrift_UserQuery',
            'fields' => array(
                'socialMediaType' => 'Platform',
                'socialMediaAccountID' => $platform_user_id,
            )));
    }

    public function getMoniplaCore() {
        if ($this->moniplaCore == null) {
            $this->moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
        }
        return $this->moniplaCore;
    }


    /**
     * @param $platform_user_ids
     * @return array
     */
    public function findUsers($platform_user_ids) {
        if (!$platform_user_ids) return;

        $data = array();
        foreach ($platform_user_ids as $platform_user_id) {
            $data[$platform_user_id] = $this->findUser($platform_user_id);
        }
        return $data;
    }

    public function findUser($platform_user_id) {
        if (!$platform_user_id) return;

        $data = array();
        $data['platform_user'] = $this->findPlatformUser($platform_user_id);
        $data['brandco_user'] = $this->findBrandcoUser($platform_user_id);
        $data['brand_users'] = $this->findBrandcoBrandUsers($platform_user_id);
        $data['cp_users'] = $this->findBrandcoCpUsers($platform_user_id);
        $data['cp_user_statuses'] = $this->findBrandcoCpUserStatuses($platform_user_id);
        return $data;
    }

    /**
     * @param $platform_user_id
     * @return mixed
     */
    public function findPlatformUser($platform_user_id) {
        if (!$platform_user_id) return;

        $user = $this->getPlatformUser($platform_user_id);
        if ($user->id < 1) return;
        $data['id'] = $user->id;
        $data['name'] = $user->name;
        $data['mail_address'] = $user->mailAddress;

        // AlliedIDのオプトイン状況を取得
        $mainte_db = new aafwDataBuilder('maintedb');
        $condition['platform_user_id'] = $user->id;
        $data['optin'] = $mainte_db->selectUserSearchPlatformOptin($condition)[0]['optin'];

        // MPFBのオプトイン状況を取得
        /** @var OldMoniplaUserOptinService $old_monipla_user_optin_service */
        $old_monipla_user_optin_service = $this->getService("OldMoniplaUserOptinService");
        $mpfb_optin = $old_monipla_user_optin_service->get_or_create($user->id, self::PLATFORM_USER_OPTIN);
        $data['mpfb_optin'] = $mpfb_optin->data->opt_in;

        $data['social_accounts'] = array();
        foreach ($user->socialAccounts as $social_account) {
            $social_media_id = SocialAccountService::getSocialMediaIdBySocialMediaType($social_account->socialMediaType);

            $account['social_media_type'] = $social_account->socialMediaType;
            $account['social_media_account_id'] = $social_account->socialMediaAccountID;
            $account['name'] = $social_account->name;
            $account['mail_address'] = $social_account->mail_address;
            $account['profile_image_url'] = $social_account->profileImageUrl;
            $account['profile_page_url'] = $social_account->profilePageUrl;
            $account['sns_mini_icon'] = SocialAccountService::$socialMiniIcon[$social_media_id];

            $data['social_accounts'][$social_media_id] = $account;
        }
        return $data;
    }

    /**
     * @param $platform_user_id
     * @return mixed
     */
    public function findBrandcoUser($platform_user_id) {
        if (!$platform_user_id) return;

        $user = $this->user_service->getUserByMoniplaUserId($platform_user_id);
        return $user ? $user->toArray() : null;
    }

    /**
     * @param $platform_user_id
     * @return mixed
     */
    public function findBrandcoBrandUsers($platform_user_id) {
        if (!$platform_user_id) return;

        $condition['platform_user_id'] = $platform_user_id;
        $brand_users = $this->db->selectUserSearchBrandUsers($condition);
        return $brand_users;
    }

    /**
     * @param $platform_user_id
     * @return mixed
     */
    public function findBrandcoCpUsers($platform_user_id) {
        if (!$platform_user_id) return;

        $condition['platform_user_id'] = $platform_user_id;
        $cp_users = $this->db->selectUserSearchCpUsers($condition);
        return $cp_users;
    }

    /**
     * @param $platform_user_id
     * @return mixed
     */
    public function findBrandcoCpUserStatuses($platform_user_id) {
        if (!$platform_user_id) return;

        $condition['platform_user_id'] = $platform_user_id;
        $cp_user_statuses = $this->db->selectUserSearchCpUserStatuses($condition);

        $data = array();
        $cp_action = new CpAction();
        foreach ($cp_user_statuses as $cp_user_status) {
            $data[$cp_user_status['cp_id']]['join_date'] = $cp_user_status['join_date'];
            $data[$cp_user_status['cp_id']]['last_join_order_no'] = $cp_user_status['last_join_order_no'];
            $data[$cp_user_status['cp_id']]['last_join_action_type'] = $cp_action->getCpActionDetailByType($cp_user_status['last_join_action_type'])['title'];
            $data[$cp_user_status['cp_id']]['cp_action_id'] = $cp_user_status['cp_action_id'];
        }
        return $data;
    }
}
