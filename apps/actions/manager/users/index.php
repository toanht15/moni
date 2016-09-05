<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.validator.ManagerUsersValidator');
AAFW::import('jp.aainc.classes.services.ManagerUserSearchService');
AAFW::import('jp.aainc.classes.services.SocialAccountService');
AAFW::import('jp.aainc.classes.util.TokenWithoutSimilarCharGenerator');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class index extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId = Manager::MENU_USER_SEARCH;

    protected $ErrorPage = 'manager/users/index.php';
    protected $ValidatorDefinitions = array(
        'search_type' => array(
            'type' => ManagerUsersValidator::VALID_CHOICE,
            'expected' => array(
                ManagerUserSearchService::SEARCH_TYPE_PL_UID,
                ManagerUserSearchService::SEARCH_TYPE_BRC_UID,
                ManagerUserSearchService::SEARCH_TYPE_SNS_UID,
                ManagerUserSearchService::SEARCH_TYPE_PL_MAIL,
                ManagerUserSearchService::SEARCH_TYPE_BRC_MAIL,
                ManagerUserSearchService::SEARCH_TYPE_BRC_NO
            )
        ),
        'platform_user_id' => array(
            'type' => ManagerUsersValidator::VALID_NUMBER,
            'expected' => '100000000'
        ),
        'brandco_user_id' => array(
            'type' => ManagerUsersValidator::VALID_NUMBER,
            'expected' => '100000000'
        ),
        'social_media_id' => array(
            'type' => ManagerUsersValidator::VALID_CHOICE,
            'expected' => array(
                SocialAccountService::SOCIAL_MEDIA_FACEBOOK,
                SocialAccountService::SOCIAL_MEDIA_TWITTER,
                SocialAccountService::SOCIAL_MEDIA_INSTAGRAM,
                SocialAccountService::SOCIAL_MEDIA_GOOGLE,
                SocialAccountService::SOCIAL_MEDIA_YAHOO,
                SocialAccountService::SOCIAL_MEDIA_GDO,
                SocialAccountService::SOCIAL_MEDIA_LINE,
                SocialAccountService::SOCIAL_MEDIA_LINKEDIN
            )
        ),
        'social_media_account_id' => array(
            'type' => ManagerUsersValidator::VALID_TEXT,
            'expected' => '255'
        ),
        'platform_mail_address' => array(
            'type' => ManagerUsersValidator::VALID_MAIL_ADDRESS,
            'expected' => '255'
        ),
        'brandco_mail_address' => array(
            'type' => ManagerUsersValidator::VALID_MAIL_ADDRESS,
            'expected' => '255'
        ),
        'brand_id' => array(
            'type' => ManagerUsersValidator::VALID_NUMBER,
            'expected' => '100000000'
        ),
        'brand_user_no' => array(
            'type' => ManagerUsersValidator::VALID_NUMBER,
            'expected' => '100000000'
        )
    );

    public function validate() {
        return true;
    }

    function doAction() {
        if (!$this->GET['search_type']) {
            return 'manager/users/index.php';
        }

        $this->setRequiredBySearchType();
        $validator = new ManagerUsersValidator();
        if (!$validator->isValid($this->GET, $this->ValidatorDefinitions)) {
            $this->Data['error_messages'] = $validator->getErrorMessages();
            $this->Data['search_error'] = null;
            return 'manager/users/index.php';
        }

        $this->Data['parameter_data'] = array('search_type' => $this->search_type,
            'platform_user_id' => $this->platform_user_id,
            'brandco_user_id' => $this->brandco_user_id,
            'social_media_id' =>$this->social_media_id,
            'social_media_account_id' =>$this->social_media_account_id,
            'platform_mail_address' =>$this->platform_mail_address,
            'brandco_mail_address' =>$this->brandco_mail_address,
            'brand_id' =>$this->brand_id,
            'brand_user_no' =>$this->brand_user_no
        );

        /** @var ManagerUserSearchService $manager_user_search_service */
        $manager_user_search_service = $this->getService('ManagerUserSearchService');

        // 対象ユーザを特定
        $platform_user_ids = $manager_user_search_service->searchPlatformUserIds($this->search_type, $this->GET);

        // 抽出されたユーザ数を確認
        if (!$platform_user_ids[0]) {
            $this->Data['search_error'] = 'お探しのユーザーは存在しません';
            return 'manager/users/index.php';
        }
        $this->Data['show_user_list'] = count($platform_user_ids) > 1;

        // 各種情報を抽出
        $users = $manager_user_search_service->findUsers($platform_user_ids);
        // 代理ログインで使用するtokenを生成
        $token_generator = new TokenWithoutSimilarCharGenerator();
        $salt = $token_generator->generateToken(512);
        $this->setSession('backdoor_login_salt', $salt);
        foreach ($users as $platform_user_id => &$user) {
            foreach ($user['brand_users'] as &$brand_user) {
                $token = Util::generateBackdoorLoginToken($brand_user['created_at'], $salt);
                $brand_user['token'] = $token;
            }
        }
        $this->Data['users'] = $users;

        return 'manager/users/index.php';
    }

    /**
     * 検索タイプごとに必須項目のバリデートを追加する
     */
    public function setRequiredBySearchType() {
        if ($this->search_type == ManagerUserSearchService::SEARCH_TYPE_PL_UID) {
            $this->ValidatorDefinitions['platform_user_id']['required'] = true;

        } elseif ($this->search_type == ManagerUserSearchService::SEARCH_TYPE_BRC_UID) {
            $this->ValidatorDefinitions['brandco_user_id']['required'] = true;

        } elseif ($this->search_type == ManagerUserSearchService::SEARCH_TYPE_SNS_UID) {
            $this->ValidatorDefinitions['social_media_id']['required'] = true;
            $this->ValidatorDefinitions['social_media_account_id']['required'] = true;

        } elseif ($this->search_type == ManagerUserSearchService::SEARCH_TYPE_PL_MAIL) {
            $this->ValidatorDefinitions['platform_mail_address']['required'] = true;

        } elseif ($this->search_type == ManagerUserSearchService::SEARCH_TYPE_BRC_MAIL) {
            $this->ValidatorDefinitions['brandco_mail_address']['required'] = true;

        } elseif ($this->search_type == ManagerUserSearchService::SEARCH_TYPE_BRC_NO) {
            $this->ValidatorDefinitions['brand_id']['required'] = true;
            $this->ValidatorDefinitions['brand_user_no']['required'] = true;
        }
    }
}
