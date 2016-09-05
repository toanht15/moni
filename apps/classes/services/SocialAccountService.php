<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class SocialAccountService extends aafwServiceBase {
    /** @var aafwEntityStoreBase $social_account  */
    protected $social_account;
    private static $_instance = null;

    const SOCIAL_MEDIA_PLATFORM     = -1;
    const SOCIAL_MEDIA_FACEBOOK     = 1;
    const SOCIAL_MEDIA_TWITTER      = 3;
    const SOCIAL_MEDIA_GOOGLE       = 4;
    const SOCIAL_MEDIA_YAHOO        = 5;
    const SOCIAL_MEDIA_GDO          = 6;
    const SOCIAL_MEDIA_INSTAGRAM    = 7;
    const SOCIAL_MEDIA_LINE         = 8;
    const SOCIAL_MEDIA_LINKEDIN     = 9;

    public static $socialMediaOrder = array(
        self::SOCIAL_MEDIA_PLATFORM  => -1,
        self::SOCIAL_MEDIA_FACEBOOK  => 1,
        self::SOCIAL_MEDIA_TWITTER   => 2,
        self::SOCIAL_MEDIA_LINE      => 3,
        self::SOCIAL_MEDIA_INSTAGRAM => 4,
        self::SOCIAL_MEDIA_YAHOO     => 5,
        self::SOCIAL_MEDIA_GOOGLE    => 6,
        self::SOCIAL_MEDIA_GDO       => 7,
        self::SOCIAL_MEDIA_LINKEDIN  => 8,
    );

    // ユーザ検索の連携SNS抽出対象
    public static $availableSocialAccount = array(
        self::SOCIAL_MEDIA_FACEBOOK,
        self::SOCIAL_MEDIA_TWITTER,
        self::SOCIAL_MEDIA_LINE,
        self::SOCIAL_MEDIA_INSTAGRAM,
        self::SOCIAL_MEDIA_YAHOO,
        self::SOCIAL_MEDIA_GOOGLE,
        self::SOCIAL_MEDIA_GDO,
        self::SOCIAL_MEDIA_LINKEDIN
    );

    public static $userSearchSocialMedia = array(
        self::SOCIAL_MEDIA_FACEBOOK  => 1,
        self::SOCIAL_MEDIA_TWITTER   => 2,
        self::SOCIAL_MEDIA_LINE      => 3,
        self::SOCIAL_MEDIA_INSTAGRAM => 4,
        self::SOCIAL_MEDIA_YAHOO     => 5,
        self::SOCIAL_MEDIA_GOOGLE    => 6,
        self::SOCIAL_MEDIA_GDO       => 7,
        self::SOCIAL_MEDIA_LINKEDIN  => 8,
    );

    public static $socialAccountLabel = array(
        self::SOCIAL_MEDIA_FACEBOOK => "Facebook",
        self::SOCIAL_MEDIA_TWITTER => "Twitter",
        self::SOCIAL_MEDIA_LINE => "LINE",
        self::SOCIAL_MEDIA_INSTAGRAM => "Instagram",
        self::SOCIAL_MEDIA_YAHOO => "Yahoo! JAPAN",
        self::SOCIAL_MEDIA_GOOGLE => "Google+",
        self::SOCIAL_MEDIA_LINKEDIN => "LinkedIn",
        self::SOCIAL_MEDIA_GDO => "GDO"
    );

    public static $socialBigIcon = array(
        self::SOCIAL_MEDIA_FACEBOOK => "iconFB1",
        self::SOCIAL_MEDIA_TWITTER => "iconTW1",
        self::SOCIAL_MEDIA_LINE => "iconLN1",
        self::SOCIAL_MEDIA_INSTAGRAM => "iconIG1",
        self::SOCIAL_MEDIA_YAHOO => "iconYH1",
        self::SOCIAL_MEDIA_GOOGLE => "iconGP1",
        self::SOCIAL_MEDIA_LINKEDIN => "iconIN1",
        self::SOCIAL_MEDIA_GDO => "iconGdo1"
    );

    public static $socialBigIconForPlugin = array(
        self::SOCIAL_MEDIA_FACEBOOK => "iconFb1",
        self::SOCIAL_MEDIA_TWITTER => "iconTw1",
    );

    public static $socialSmallIcon = array(
        self::SOCIAL_MEDIA_FACEBOOK => "iconFB2",
        self::SOCIAL_MEDIA_TWITTER => "iconTW2",
        self::SOCIAL_MEDIA_LINE => "iconLN2",
        self::SOCIAL_MEDIA_INSTAGRAM => "iconIG2",
        self::SOCIAL_MEDIA_YAHOO => "iconYH2",
        self::SOCIAL_MEDIA_GOOGLE => "iconGP2",
        self::SOCIAL_MEDIA_LINKEDIN => "iconIN2",
        self::SOCIAL_MEDIA_GDO => "iconGdo2"
    );

    // ユーザ検索用のアイコン
    public static $socialMiniIcon = array(
        self::SOCIAL_MEDIA_FACEBOOK => 'iconFB1',
        self::SOCIAL_MEDIA_TWITTER => 'iconTW1',
        self::SOCIAL_MEDIA_LINE => 'iconLN1',
        self::SOCIAL_MEDIA_INSTAGRAM => 'iconIG1',
        self::SOCIAL_MEDIA_YAHOO => 'iconYH1',
        self::SOCIAL_MEDIA_GOOGLE => 'iconGP1',
        self::SOCIAL_MEDIA_LINKEDIN => 'iconIN1',
        self::SOCIAL_MEDIA_GDO => 'iconGD1',
    );

    public static $socialHasFriendCount = array(
        self::SOCIAL_MEDIA_FACEBOOK,
        self::SOCIAL_MEDIA_TWITTER,
        self::SOCIAL_MEDIA_INSTAGRAM
    );

    // ユーザ検索の連携SNS項目
    public static $managerUserSearchTarget = array(
        self::SOCIAL_MEDIA_FACEBOOK => 'Facebook',
        self::SOCIAL_MEDIA_TWITTER => 'Twitter',
        self::SOCIAL_MEDIA_GOOGLE => 'Google',
        self::SOCIAL_MEDIA_YAHOO => 'Yahoo',
        self::SOCIAL_MEDIA_INSTAGRAM => 'Instagram',
        self::SOCIAL_MEDIA_LINE => 'LINE',
        self::SOCIAL_MEDIA_LINKEDIN => 'LinkedIn',
    );

    public static $sns_domain_list = array(
        self::SOCIAL_MEDIA_TWITTER   => 'twitter.com',
        self::SOCIAL_MEDIA_FACEBOOK  => 'www.facebook.com'
    );

    public static function getSocialMediaIdBySocialMediaType($social_media_type) {
        if (!$social_media_type) return;

        return array_search($social_media_type, self::$managerUserSearchTarget);
    }

    const SOCIAL_MEDIA_KEY_ID           = 'id';
    const SOCIAL_MEDIA_KEY_TYPE         = 'type';
    const SOCIAL_MEDIA_KEY_CLIENT_ID    = 'client_id';

    public static $social_media = array(
        ['id' => self::SOCIAL_MEDIA_PLATFORM, 'type' => 'Platform', 'client_id' => 'platform'],
        ['id' => self::SOCIAL_MEDIA_FACEBOOK, 'type' => 'Facebook', 'client_id' => 'fb'],
        ['id' => self::SOCIAL_MEDIA_TWITTER, 'type' => 'Twitter', 'client_id' => 'tw'],
        ['id' => self::SOCIAL_MEDIA_LINE, 'type' => 'LINE', 'client_id' => 'line'],
        ['id' => self::SOCIAL_MEDIA_INSTAGRAM, 'type' => 'Instagram', 'client_id' => 'insta'],
        ['id' => self::SOCIAL_MEDIA_YAHOO, 'type' => 'Yahoo', 'client_id' => 'yh'],
        ['id' => self::SOCIAL_MEDIA_GOOGLE, 'type' => 'Google', 'client_id' => 'ggl'],
        ['id' => self::SOCIAL_MEDIA_LINKEDIN, 'type' => 'LinkedIn', 'client_id' => 'linkedin'],
    );

    public function __construct() {
        $this->social_account = $this->getModel('SocialAccounts');
    }

    public function getSocialAccount($user_id, $social_media_id, $social_media_account_id) {

        $filter = array(
            'conditions' => array(
                'user_id' => $user_id,
                'social_media_id' => $social_media_id,
                'social_media_account_id' => $social_media_account_id
            ),
        );

        return $this->social_account->findOne($filter);
    }

    public function getSocialAccuntsByUserId($userId) {
        $filter = array(
            'conditions' => array(
                'user_id' => $userId,
            ),
        );

        return $this->social_account->find($filter);
    }

    public function getSocialAccountsByUserIdOrderBySocialMediaAccountId($userId) {
        $filter = array(
            'conditions' => array(
                'user_id' => $userId,
            ),
            'order' => array(
                'name' => 'social_media_account_id'
            )
        );

        return $this->social_account->find($filter);
    }

    public function setSocialAccountsList($social_accounts, $user_id) {
        foreach ($social_accounts as $social_account) {
            $this->setSocialAccounts($social_account, $user_id);
        }
    }

    public function setSocialAccounts($social_accounts, $user_id) {
        if($social_accounts->socialMediaType == 'Facebook') {
            $social_media_id = self::SOCIAL_MEDIA_FACEBOOK;
        } elseif($social_accounts->socialMediaType == 'Twitter') {
            $social_media_id = self::SOCIAL_MEDIA_TWITTER;
        } elseif($social_accounts->socialMediaType == 'Google') {
            $social_media_id = self::SOCIAL_MEDIA_GOOGLE;
        } elseif($social_accounts->socialMediaType == 'Yahoo') {
            $social_media_id = self::SOCIAL_MEDIA_YAHOO;
        } elseif($social_accounts->socialMediaType == 'GDO') {
            $social_media_id = self::SOCIAL_MEDIA_GDO;
        } elseif($social_accounts->socialMediaType == 'Instagram') {
            $social_media_id = self::SOCIAL_MEDIA_INSTAGRAM;
        } elseif ($social_accounts->socialMediaType == 'LINE') {
            $social_media_id = self::SOCIAL_MEDIA_LINE;
        } elseif ($social_accounts->socialMediaType == 'LinkedIn') {
            $social_media_id = self::SOCIAL_MEDIA_LINKEDIN;
        } else {
            return;
        }

        $social_account = $this->getSocialAccount($user_id, $social_media_id, $social_accounts->socialMediaAccountID);

        if(!$social_account) {
            $social_account = $this->createEmptySocialAccounts();
            $social_account->user_id = $user_id;
        }
        $social_account->social_media_id = $social_media_id;
        $social_account->social_media_account_id = $social_accounts->socialMediaAccountID;
        $social_account->name = $social_accounts->name;
        $social_account->mail_address = $social_accounts->mailAddress;
        $social_account->profile_image_url = $social_accounts->profileImageUrl;
        $social_account->profile_page_url = $social_accounts->profilePageUrl;
        $social_account->validated = $social_accounts->validated;
        $this->createSocialAccounts($social_account);
    }

    public function createSocialAccounts($social_account) {
        $this->social_account->save($social_account);
    }

    public function createEmptySocialAccounts() {
        return $this->social_account->createEmptyObject();
    }

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function deleteSocialAccountsByUserId($user_id) {
        $social_accounts = $this->getSocialAccuntsByUserId($user_id);
        foreach ($social_accounts as $social_account) {
            $this->social_account->deletePhysical($social_account);
        }
    }

    public function getSocialAccountBySocialMediaIdAndSocialMediaAccountId($social_media_id,$social_media_account_id) {

        $filter = array(
            'conditions' => array(
                'social_media_id' => $social_media_id,
                'social_media_account_id' => $social_media_account_id,
            ),
        );
        return $this->social_account->findOne($filter);
    }

    /**
     * @param $type
     * @param $value
     * @return array
     */
    public function getSocialMedia($type, $value) {
        $cur_social_media =  array_filter(self::$social_media, function($social_media) use ($type, $value) {
            return $social_media[$type] == $value;
        });

        return current($cur_social_media);
    }

    /**
     *
     * @param $user_list
     * @param $social_media_id
     * @return aafwEntityContainer|array
     */
    public function getSocialAccountsFromUserList($user_list, $social_media_id){
        $filter = array(
            'conditions' => array(
                'user_id' => $user_list,
                'social_media_id' => $social_media_id,
            ),
        );

        return $this->social_account->find($filter);
    }

    /**
     * @param $user_id
     * @param $social_app_id
     * @return aafwEntityContainer|array
     */
    public function getSocialAccountByUserIdAndSocialAppId($user_id, $social_media_id){
        $filter = array(
            'conditions' => array(
                'user_id' => $user_id,
                'social_media_id' => $social_media_id,
            ),
        );

        return $this->social_account->findOne($filter);
    }

    /**
     * @param $social_media_type
     * @param string $protocol
     * @return string
     */
    public static function getSnsUrl($social_media_type, $protocol = 'https') {
        return $protocol . '://' . self::$sns_domain_list[$social_media_type] . '/';
    }
}