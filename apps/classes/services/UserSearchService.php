<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class UserSearchService extends aafwServiceBase {

    const SNS_TYPE_ICON_FACEBOOK = '/img/sns/iconSnsFB4.png';
    const SNS_TYPE_ICON_TWITTER = '/img/sns/iconSnsTW4.png';
    const SNS_TYPE_ICON_YAHOO = '/img/sns/iconSnsYH4.png';
    const SNS_TYPE_ICON_GOOGLE = '/img/sns/iconSnsGP4.png';

    const PLATFORM_USER_NULL = -1;
    const SEARCH_DEFAULT = '-';

    const SNS_TYPE_FACEBOOK = 'Facebook';
    const SNS_TYPE_TWITTER = 'Twitter';
    const SNS_TYPE_YAHOO = 'Yahoo';
    const SNS_TYPE_GOOGLE = 'Google';
    const SNS_TYPE_INSTAGRAM = 'Instagram';

    const ALL_COMPLETED = "全部完了";
    const NON_COMPLETED = "未完了";

    const USER_SEARCH_DEFAULT = -1;
    const USER_SEARCH_PLATFORM_ID = 1;
    const USER_SEARCH_BRANDCO_ID = 2;
    const USER_SEARCH_SNS = 3;
    const USER_SEARCH_AA_MAIL = 4;
    const USER_SEARCH_BRAND_MAIL = 5;
    const USER_SEARCH_BRAND = 6;

    const TOKEN_KEY = '_alt'; // Agent Login Token
    const TOKEN_EXPIRE_TIME = 15;

    /**
     * プラットフォームユーザ情報取得
     * @param $moniplaUserId
     * @return mixed
     */

    public function getPlatformUserInfo($moniplaUserId) {
        return $this->getMoniplaCore()->getUserByQuery(array(
            'class' => 'Thrift_UserQuery',
            'fields' => array(
                'socialMediaType' => 'Platform',
                'socialMediaAccountID' => $moniplaUserId,
            )));
    }

    public function getMoniplaCore() {
        if ($this->moniplaCore == null) {
            AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
            $this->moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
        }
        return $this->moniplaCore;
    }

    public function getUsersByMailAddress($mail_address) {
        return $this->getMoniplaCore()->getUserByQuery(array(
            'class' => 'Thrift_UserQuery',
            'fields' => array(
                'mailAddress' => $mail_address
            )
        ));
    }

    public static function generateOnetimeToken($userId) {
        $token_generator = new TokenWithoutSimilarCharGenerator();
        $token = $token_generator->generateToken(64);
        $redis = CacheManager::getRedis();
        $redis->multi(Redis::PIPELINE);
        $redis->set(self::generateKey($token), $userId);
        $redis->expire(self::generateKey($token), self::TOKEN_EXPIRE_TIME);
        $redis->exec();

        return self::TOKEN_KEY . '=' . $token;

    }

    public static function verifyOnetimeToken($token) {
        $redis = CacheManager::getRedis();
        $redis->multi(Redis::PIPELINE);
        $redis->get(self::generateKey($token));
        $redis->del(self::generateKey($token));
        $result = $redis->exec();
        $userID = $result[0];

        return $userID;
    }

    private static function generateKey($token) {
        return self::TOKEN_KEY . ':' . $token;
    }

    public function generateOnetimeUrl($brand_id, $user_id){
        /** @var BrandsUsersRelationService $brandsUsersRelationService */
        $brandsUsersRelationService = $this->getService('BrandsUsersRelationService');
        /** @var BrandService $brandService */
        $brandService = $this->getService('BrandService');
        $brandsUsersRelation = $brandsUsersRelationService->getBrandsUsersRelation($brand_id, $user_id);
        $brand = $brandService->getBrandById($brand_id);
        $auth_brand_url = $brandsUsersRelation->getBrand()->getUrl();

        return $auth_brand_url . 'inquiry/?' . UserSearchService::generateOnetimeToken($user_id);
    }

    public function getAuthBrandUrl($brand_id, $user_id) {
        /** @var BrandsUsersRelationService $brandsUsersRelationService */
        $brandsUsersRelationService = $this->getService('BrandsUsersRelationService');
        /** @var BrandcoAuthService $brandcoAuthService */
        $brandcoAuthService = $this->getService('BrandcoAuthService');
        $brandsUsersRelation = $brandsUsersRelationService->getBrandsUsersRelation($brand_id, $user_id);
        $loginBrandIds[$brand_id] = 1;
        $_SESSION['pl_loginBrandIds'] = $loginBrandIds;
        $_SESSION['pl_monipla_userId'] = $user_id;
        $userInfo = $brandcoAuthService->castSocialAccounts($brandcoAuthService->getUserInfoByQuery($brandsUsersRelation->getUser()->monipla_user_id));
        $_SESSION['pl_monipla_userInfo'] = $userInfo;
        $auth_brand_url = $brandsUsersRelation->getBrand()->getUrl();

        return $auth_brand_url;
    }

}

