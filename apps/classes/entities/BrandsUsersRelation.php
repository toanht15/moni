<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
AAFW::import('jp.aainc.classes.services.ShippingAddressService');
AAFW::import('jp.aainc.classes.services.SocialAccountService');
AAFW::import('jp.aainc.classes.services.UserSearchInfoService');
AAFW::import('jp.aainc.classes.services.BrandsUsersRelationService');

class BrandsUsersRelation extends aafwEntityBase {

    const FORCE_WITH_INFO = 2;
    const SIGNUP_WITH_INFO = 1;
    const SIGNUP_WITHOUT_INFO = 0;

    protected $socialAccounts;
    protected $userAttributes;

    protected $_Relations = array(
        'Users' => array(
            'user_id' => 'id',
        ),
        'Brands' => array(
            'brand_id' => 'id',
        ),
    );

    public function __construct(brands_users_relations $brand_user = null) {

        $this->setValues($brand_user);
    }

    public function getSocialAccounts() {
        $this->socialAccounts = SocialAccountService::getInstance()->getSocialAccuntsByUserId($this->user_id);
        return $this->socialAccounts;
    }

    public function getFacebookSocialAccounts() {
        $this->socialAccounts = SocialAccountService::getInstance()->getSocialAccounts($this->user_id, SocialAccountService::SOCIAL_MEDIA_FACEBOOK);
        return $this->socialAccounts;
    }

    public function getTwitterSocialAccounts() {
        $this->socialAccounts = SocialAccountService::getInstance()->getSocialAccounts($this->user_id, SocialAccountService::SOCIAL_MEDIA_TWITTER);
        return $this->socialAccounts;
    }

    public function getYahooSocialAccounts() {
        $this->socialAccounts = SocialAccountService::getInstance()->getSocialAccounts($this->user_id, SocialAccountService::SOCIAL_MEDIA_YAHOO);
        return $this->socialAccounts;
    }

    public function getGoogleSocialAccounts() {
        $this->socialAccounts = SocialAccountService::getInstance()->getSocialAccounts($this->user_id, SocialAccountService::SOCIAL_MEDIA_GOOGLE);
        return $this->socialAccounts;
    }

    public function getGdoSocialAccounts() {
        $this->socialAccounts = SocialAccountService::getInstance()->getSocialAccounts($this->user_id, SocialAccountService::SOCIAL_MEDIA_GDO);
        return $this->socialAccounts;
    }

    public function getInstagramSocialAccounts() {
        $this->socialAccounts = SocialAccountService::getInstance()->getSocialAccounts($this->user_id, SocialAccountService::SOCIAL_MEDIA_INSTAGRAM);
        return $this->socialAccounts;
    }

    public function getLineSocialAccounts() {
        $this->socialAccounts = SocialAccountService::getInstance()->getSocialAccounts($this->user_id, SocialAccountService::SOCIAL_MEDIA_LINE);
        return $this->socialAccounts;
    }

    public function getLinkedInSocialAccounts() {
        $this->socialAccounts = SocialAccountService::getInstance()->getSocialAccounts($this->user_id, SocialAccountService::SOCIAL_MEDIA_LINKEDIN);
        return $this->socialAccounts;
    }

    public function getUserAttributeInfo() {
        $user_search_info = UserSearchInfoService::getInstance()->getUserSearchInfo($this->user_id);
        $sex = $user_search_info ? $user_search_info->sex : '';
        if($user_search_info && $user_search_info->birthday != '0000-00-00') {
            $now      = date('Ymd');
            $birthday = date('Ymd', strtotime($user_search_info->birthday));
            $age = floor(($now - $birthday) / 10000);
        } else {
            $age = '';
        }
        return array($sex, $age);
    }

    public function getBrandsUsersRelation() {
        $brands_users_relation = BrandsUsersRelationService::getInstance()->getBrandsUsersRelationById($this->brands_users_relations_id);

        return $brands_users_relation;
    }

    public function getPrefecture() {
        $prefecture = ShippingAddressService::getInstance()->getPrefectureByUserId($this->user_id);

        return $prefecture;
    }

    public function getBrandcoUser() {
        $brandco_user = UserService::getInstance()->getUserByBrandcoUserId($this->user_id);

        return $brandco_user;
    }

    public function isAdmin() {
        return $this->admin_flg === '1';
    }

    public function isProfileQuestionRequired() {
        return $this->withdraw_flg || $this->personal_info_flg != self::SIGNUP_WITH_INFO;
    }
}
