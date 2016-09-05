<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.classes.BrandInfoContainer');
AAFW::import('jp.aainc.classes.services.monipla.OldMoniplaUserOptinService');

class pre_signup_post extends BrandcoPOSTActionBase {
    use BrandcoAuthTrait;

    // Action関連の変数
    public $NeedOption = array();
    public $CsrfProtect = true;
    public $isLoginPage = true;
    protected $ContainerName = 'signup';
    
    protected $Form = array(
        'package' => 'auth',
        'action' => 'signup'
    );
    protected $ValidatorDefinition = array(
        'name' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 40
        ),
        'mail_address' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
            'validator' => array('MailAddress')
        )
    );

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

    public function doThisFirst() {
        // グローバル変数にセッション値を格納
        $this->redirect_url = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('authRedirectUrl') ?: Util::rewriteUrl('', '');
        $this->client_id    = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('clientId');
        $this->accessToken  = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('accessToken');
        $this->refreshToken = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('refreshToken');

        // サービスクラスの呼び出し
        $this->admin_invite_token_service = $this->getService('AdminInviteTokenService');

        // ロガーの呼び出し
        $this->logger = aafwLog4phpLogger::getDefaultLogger();

        if ($this->hasAdminInviteToken($this->getBrand()->id)) {
            $this->NeedPublic = true;
        }
    }

    public function validate () {
        if (!$this->accessToken || !$this->refreshToken) {
            return '403';
        }

        // AccessTokenからUserInfoを取得
        $this->userInfo = $this->getUserInfoByAccessToken($this->accessToken);
        if (!$this->userInfo || !$this->userInfo->id) {
            $this->logger->error("Thrift api error: can't get 'user info'. (access_token = {$this->accessToken})");
            return false;
        }

        $user = $this->getUserByMailAddress($this->POST['mail_address']);
        if ($user && $user->id !== $this->userInfo->id) {
            $this->Validator->setError('mail_address', 'EXISTED_MAIL_ADDRESS_INQ');
        }

        return $this->Validator->isValid();
    }

    function doAction() {
        // AAIDのUser情報を更新
        $user_manager = new UserManager($this->userInfo, $this->getMoniplaCore());
        $ret = $user_manager->changeName($this->POST['name']);
        if (!$ret) {
            $this->logger->error("Thrift api error: can't change 'name'. (name = {$this->POST['name']})");
            return '500';
        }

        $ret = $user_manager->changeMailAddress($this->POST['mail_address']);
        if (!$ret) {
            $this->logger->error("Thrift api error: can't change 'mail_address'. (mail_address = {$this->POST['mail_address']})");
            return '500';
        }

        $this->userInfo->name = $this->POST['name'];
        $this->userInfo->mailAddress = $this->POST['mail_address'];

        $required_personal_form = $this->isPersonalFormRequired();

        $user_transaction = aafwEntityStoreFactory::create('Users');

        try {
            $user_transaction->begin(1);

            $user = $this->getOrCreateMoniplaUser($this->userInfo, false, $this->getBrand()->app_id, $this->accessToken, $this->refreshToken, $this->client_id);

            // Welcomeメールの送信
            $this->sendWelcomeMail($user, $this->getBrand());

            $user_transaction->commit();

            // オプトイン情報 (デフォルトは0) を保存
            $this->updateOptin($this->userInfo->id, $this->POST['optin'] ? UserManager::OPT_IN : UserManager::OPT_OUT, OldMoniplaUserOptinService::FROM_ID_SIGN_UP_BRAND);

            $this->Data['saved'] = 1;
        } catch (Exception $e) {
            $user_transaction->rollback();

            // ログイン失敗
            $this->logger->error("pre_signup_post#doAction error" . "client_id=" . $this->client_id);
            $this->logger->error($e);

            $this->setSession('pl_monipla_userInfo', null);
            $this->setSession('authRedirectUrl', null);
            $this->setSession('clientId', null);
            $this->setSession('accessToken', null);
            $this->setSession('refreshToken', null);
            $this->setSession('cp_id', null);
            return 'redirect: ' . $this->redirect_url;
        }

        // Avoid auto login in comment plugin mode (need to confirm comment content first)
        $is_cmt_plugin_mode = $this->getBrandSession('isCmtPluginMode') ?: null;
        if ($required_personal_form ||  $is_cmt_plugin_mode === true) {
            return 'redirect: ' . Util::rewriteUrl('auth', 'signup');
        } else {
            // BrandsUsersRelationの取得 (作成)
            $brands_users_relation = $this->getOrCreateBrandUserRelation($this->getBrand()->id, $user->id, BrandsUsersRelation::SIGNUP_WITH_INFO);

            // ログイン処理
            $this->login($this->userInfo, $brands_users_relation);

            if ($this->hasAdminInviteToken($this->getBrand()->id)) {
                $this->redirect_url = Util::rewriteUrl('auth', 'certificate_administrator');
            } else {
                $parsed_url = parse_url($this->redirect_url);
                $parsed_url['query'] = isset($parsed_url['query']) ? $parsed_url['query'] . '&tid=signup_complete' : 'tid=signup_complete';
                $this->redirect_url = $this->unparse_url($parsed_url);
            }

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
