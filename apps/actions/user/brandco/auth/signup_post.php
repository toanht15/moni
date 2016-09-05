<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.core.UserAttributeManager');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');
AAFW::import('jp.aainc.classes.services.QuestionTypeService');
AAFW::import('jp.aainc.classes.entities.CpUser');
AAFW::import('jp.aainc.classes.entities.BrandsUsersRelation');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');

class signup_post extends BrandcoPOSTActionBase {
    use BrandcoAuthTrait;

    public $NeedOption = array();
    public $CsrfProtect = true;
    public $isLoginPage = true;
    protected $ContainerName = 'signup';
    protected $Form = array(
        'package' => 'auth',
        'action' => 'signup'
    );


    protected $ValidatorDefinition = array();

    // 認証関連の変数
    private $redirect_url;
    private $client_id;
    private $accessToken;
    private $refreshToken;
    private $userInfo;

    // ロガー
    private $logger;

    /** @var  AdminInviteTokenService $admin_invite_token_service */
    private $admin_invite_token_service;
    /** @var  SocialAccountService $social_account_service */
    private $social_account_service;
    /** @var  UserApplicationService $user_application_service */
    private $user_application_service;
    /** @var  BrandPageSettingService $brand_page_setting_service */
    private $brand_page_setting_service;
    /** @var  UserManager $userManager */
    private $userManager;
    /** @var  UserAttributeManager $userAttributeManager */
    private $userAttributeManager;
    /** @var  ShippingAddressManager $shippingAddressManager */
    private $shippingAddressManager;

    public function doThisFirst() {
        // グローバル変数にセッション値を格納
        $this->redirect_url = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('authRedirectUrl') ?: Util::rewriteUrl('', '');
        $this->client_id    = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('clientId');
        $this->accessToken  = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('accessToken');
        $this->refreshToken = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('refreshToken');

        // サービスクラスの呼び出し
        $this->admin_invite_token_service   = $this->getService('AdminInviteTokenService');
        $this->social_account_service       = $this->getService('SocialAccountService');
        $this->user_application_service     = $this->getService('UserApplicationService');
        $this->brand_page_setting_service   = $this->getService('BrandPageSettingService');

        // UserInfoの取得
        if ($this->accessToken && $this->refreshToken) {
            $this->userInfo = $this->getUserInfoByAccessToken($this->accessToken);
        } else {
            throw new Exception("Error: The 'access token' is null");
        }

        $this->userManager = new UserManager($this->userInfo, $this->getMoniplaCore());

        // ロガーの呼び出し
        $this->logger = aafwLog4phpLogger::getDefaultLogger();

        if ($this->hasAdminInviteToken($this->getBrand()->id)) {
            $this->NeedPublic  = true;
        }

        // ページ設定の格納
        $this->Data['pageSettings'] = $this->brand_page_setting_service->getPageSettingsByBrandId($this->getBrand()->id);
        $this->setValidatorDefinition();
    }

    public function validate () {
        return $this->validateData();
    }

    function doAction() {
        // AccessTokenからUserInfoを取得
        $userInfo = $this->getUserInfoByMoniplaUserId($this->userInfo->id);
        if (!$userInfo) {
            $this->logger->error("Thrift api error: can't get 'user info'. (monipla_user_id = {$this->userInfo->id})");
            return '500';
        }
        $this->userInfo = $userInfo;

        // Comment Plugin - save comment data to session
        $is_cmt_plugin_mode = $this->getBrandSession('isCmtPluginMode') ?: null;
        if ($is_cmt_plugin_mode) {
            $comment_data = $this->getBrandSession('commentData');
            $comment_data['social_media_ids'] = $this->POST['social_media_ids'];
            $this->setBrandSession('commentData', $comment_data);
        }

        $user_transaction = aafwEntityStoreFactory::create('Users');
        try {

            $this->userAttributeManager = new UserAttributeManager($this->userInfo, $this->getMoniplaCore(), $this->Data['pageSettings']);
            $this->shippingAddressManager = new ShippingAddressManager($this->userInfo, $this->getMoniplaCore());

            $user_transaction->begin(1);

            // Userの取得
            $user = $this->getOrCreateUser($this->userInfo, false);

            // Personal情報を更新
            $this->updatePersonalInfo($this->Data['pageSettings'], $this);

            // BrandsUsersRelationの取得 (作成)
            $brands_users_relation = $this->getOrCreateBrandUserRelation($this->getBrand()->id, $user->id, BrandsUsersRelation::SIGNUP_WITH_INFO);
            
            $this->createOrUpdateUserApplicationAndSocialAccounts($userInfo, $user->id, $this->getBrand()->app_id, $this->accessToken, $this->refreshToken, $this->client_id);

            // ログイン処理
            $this->login($this->userInfo, $brands_users_relation);

            // プロフィールアンケートの保存
            $this->createProfileQuestionAnswers($brands_users_relation);

            $user_transaction->commit();

            if ($this->hasAdminInviteToken($this->getBrand()->id)) {
                $this->redirect_url = Util::rewriteUrl('auth', 'certificate_administrator');
            } else {
                $parsed_url = parse_url($this->redirect_url);
                $parsed_url['query'] = isset($parsed_url['query']) ? $parsed_url['query'] . '&tid=signup_complete' : 'tid=signup_complete';
                $this->redirect_url = $this->unparse_url($parsed_url);
            }

            $this->Data['saved'] = 1;

            return 'redirect: ' . $this->redirect_url;
        } catch(Exception $e){
            $user_transaction->rollback();

            // ログイン失敗
            $this->logger->error("redirect#doAction error" . "client_id=" . $this->client_id);
            $this->logger->error($e);

            $this->setSession('pl_monipla_userInfo', null);
            $this->setSession('authRedirectUrl', null);
            $this->setSession('clientId', null);
            $this->setSession('accessToken', null);
            $this->setSession('refreshToken', null);
            $this->setSession('cp_id', null);

            return 'redirect: ' . $this->redirect_url;
        }
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function hasAdminInviteToken($brand_id) {
        return $this->admin_invite_token_service->getValidInvitedToken($brand_id);
    }
}