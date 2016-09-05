<?php
AAFW::import('jp.aainc.classes.brandco.auth.BrandcoAuthActionBase');

class save_login extends BrandcoAuthActionBase {

    protected $ValidatorDefinition = array(
        'mail_address' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
            'validator' => array('MailAddress')
        ),
        'password' => array(
            'required' => 1,
            'type' => 'str'
        )
    );

    // ログイン関連の値
    private $userId;

    function getAuthAction() {
        return self::AUTH_ACTION_LOGIN;
    }

    function validate() {
        // AAIDにアカウントが存在するか検証
        $monipla_user_id = $this->getMoniplaUserIdByMailAddressAndPassword($this->POST['mail_address'], $this->POST['password']);
        if (!$monipla_user_id) {
            $this->Validator->setError('password', 'INVALID_PASSWORD');
        }

        $this->userId = $monipla_user_id;

        return $this->Validator->isValid();
    }

    function doSubAction() {
        // 認証コードの取得 (作成)
        $code = $this->createAuthorizationCodeByMoniplaUserIdAndClientId($this->userId, $this->client_id);
        if (!$code) {
            aafwLog4phpLogger::getDefaultLogger()->error("Thrift api error: can't get 'authorization code' (monipla_user_id = {$this->userId}, client_id = {$this->client_id}).");
            return '500';
        }
        // Userの取得
        $user = $this->user_service->getUserByMoniplaUserId($this->userId);
        // BrandsUsersRelationの取得
        $brands_users_relation = $this->brands_users_relation_service->getBrandsUsersRelation($this->getBrand()->id, $user->id);

        // Avoid auto login in comment plugin mode (need to confirm comment content first)
        $is_cmt_plugin_mode = $this->getBrandSession('isCmtPluginMode') ?: null;

        // ログイン可能な場合
        if ($this->canLogin($user, $brands_users_relation) && $is_cmt_plugin_mode !== true) {
            // UserInfoの取得
            $userInfo = $this->getUserInfoByMoniplaUserId($this->userId);
            if (!$userInfo) {
                aafwLog4phpLogger::getDefaultLogger()->error("Thrift api error: can't get 'user info' (monipla_user_id = {$this->userId}, brand_id = {$this->getBrand()->id}).");
                return '500';
            }

            // TODO 念のためメールアドレスが空でないかどうか確認 (出来れば消したい)
            if ($userInfo->mailAddress) {
                // アクセストークンの取得 (作成)
                $tokens = $this->createAccessTokenByCodeAndClientId($code, $this->client_id);
                $this->setTokens($tokens);
                if (!$tokens) {
                    aafwLog4phpLogger::getDefaultLogger()->error("Thrift api error: can't get 'access token' (code = {$code}, client_id = {$this->client_id}).");
                    return '500';
                }

                // UserApplicationの作成
                $this->user_application_service->createOrUpdateUserApplication($user->id, $this->getBrand()->app_id, $tokens['accessToken'], $tokens['refreshToken'], $this->client_id);

                // ログイン処理
                $this->login($userInfo, $brands_users_relation);

                // マネージャーログインの場合
                if ($this->isLoginManager() && $user->aa_flg == 0) {
                    $user->aa_flg = 1;
                    $this->user_service->updateUser($user);
                }

                // 管理者トークンを持っていた場合
                if ($brands_users_relation->admin_flg && $this->hasAdminInviteToken($this->getBrand()->id)) {
                    $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('invite_token' . $this->getBrand()->id, null);
                    $this->redirect_url = $this->getRedirectUrl();
                }

                return null;
            }
        }

        // ログイン不可な場合
        return 'redirect: ' . Util::rewriteUrl('auth', 'signup', array(), array('code' => $code,'mail_login'=>'true'));
    }
}
