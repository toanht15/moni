<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.classes.BrandInfoContainer');
AAFW::import('jp.aainc.classes.services.merge.AccountMergeFacade');
AAFW::import('jp.aainc.classes.services.monipla.OldMoniplaUserOptinService');

class api_update_user_profile extends ExecuteActionBase {
    use BrandcoAuthTrait;

    // Action関連の変数
    public $NeedOption = array();
    public $CsrfProtect = true;
    protected $AllowContent = array('JSON');
    protected $ContainerName = 'api_update_user_profile';
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
    /** @var  SocialAccountService $social_account_service */
    private $social_account_service;
    /** @var  UserApplicationService $user_application_service */
    private $user_application_service;

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
        
        // ロガーの呼び出し
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function validate() {
        if (!$this->accessToken || !$this->refreshToken) {
            $this->Validator->setError('mail_address', 'ACCESS_TOKEN_EXPIRED');

            return false;
        }

        // AccessTokenからUserInfoを取得
        $this->userInfo = $this->getUserInfoByAccessToken($this->accessToken);
        if (!$this->userInfo || !$this->userInfo->id) {
            $this->logger->error("Thrift api error: can't get 'user info'. (access_token = {$this->accessToken})");
            $this->Validator->setError('mail_address', 'ACCESS_TOKEN_EXPIRED');

            return false;
        }

        $user = $this->getUserByMailAddress($this->POST['mail_address']);
        if ($user && $user->id !== $this->userInfo->id) {
            $this->Validator->setError('mail_address', 'EXISTED_MAIL_ADDRESS_INQ');
            /** @var AccountMergeFacade $accountMergeFacade */
            $accountMergeFacade = new AccountMergeFacade();
            $isSendMergeGuideMail = $accountMergeFacade->sendMergeGuideMailIfPossible($user->id, $this->userInfo->id,$this->POST['cp_id'],$this->SESSION['clientId']);
            if( $isSendMergeGuideMail ){
                $this->Validator->setError('mail_address', 'SEND_ACCOUNT_MERGE_MAIL');
            }
        }

        return $this->Validator->isValid();
    }

    public function getFormURL () {
        $errors = array();
        foreach ($this->Validator->getError() as $key => $value) {
            if ($value === 'ACCESS_TOKEN_EXPIRED') {
                // ログアウト後のリダイレクトURLを設定
                $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('logoutRedirectUrl', Util::rewriteUrl('', 'campaigns', array($this->cp_id)));
                // ログアウトURLを設定
                $errors[$key] = sprintf($this->Validator->getMessage($key), Util::rewriteUrl('my', 'logout'));
            } elseif ($value === 'EXISTED_MAIL_ADDRESS_INQ' || $value === 'SEND_ACCOUNT_MERGE_MAIL') {
                $errors[$key] = sprintf($this->Validator->getMessage($key), Util::rewriteUrl('inquiry', 'index', [], ['cp_id' => $this->POST['cp_id']]));
            } else {
                $errors[$key] = $this->Validator->getMessage($key);
            }

        }
        $json_data = $this->createAjaxResponse("ng", array(), $errors);
        $this->assign('json_data', $json_data);

        return false;
    }

    function doAction() {
        try {
            // AAIDのUser情報を更新
            $user_manager = new UserManager($this->userInfo, $this->getMoniplaCore());
            $ret = $user_manager->changeName($this->POST['name']);
            if (!$ret) {
                $this->logger->error("Thrift api error: can't change 'name'. (name = {$this->POST['name']})");
                throw new Exception("can't change name");
            }

            $ret = $user_manager->changeMailAddress($this->POST['mail_address']);
            if (!$ret) {
                $this->logger->error("Thrift api error: can't change 'mail_address'. (mail_address = {$this->POST['mail_address']})");
                throw new Exception("can't change mail_address");
            }

            $this->userInfo->name = $this->POST['name'];
            $this->userInfo->mailAddress = $this->POST['mail_address'];
        } catch (Exception $e) {
            $json_data = $this->createAjaxResponse("ng", array());
            $this->assign('json_data', $json_data);

            return 'dummy.php';
        }

        $user_transaction = aafwEntityStoreFactory::create('Users');

        try {
            $user_transaction->begin(1);

            $user = $this->getOrCreateMoniplaUser($this->userInfo, false, $this->getBrand()->app_id, $this->accessToken, $this->refreshToken, $this->client_id);

            // Welcomeメールの送信
            $this->sendWelcomeMail($user, $this->getBrand(), $this->POST['cp_id']);

            $user_transaction->commit();

            // オプトイン情報 (デフォルトは0) を保存
            $this->updateOptin($this->userInfo->id, $this->POST['optin'] ? UserManager::OPT_IN : UserManager::OPT_OUT, OldMoniplaUserOptinService::FROM_ID_SIGN_UP_CP, $this->POST['cp_id']);

            $this->Data['saved'] = 1;

            // userInfo情報を更新
            $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('pl_monipla_userInfo', $this->getBrandcoAuthService()->castSocialAccounts($this->userInfo));

            $json_data = $this->createAjaxResponse("ok", array());
            $this->assign('json_data', $json_data);
        } catch (Exception $e) {
            $user_transaction->rollback();

            // ログイン失敗
            $this->logger->error("api_update_user_profile#doAction error" . "client_id=" . $this->client_id);
            $this->logger->error($e);

            $json_data = $this->createAjaxResponse("ng", array());
            $this->assign('json_data', $json_data);
        }

        if ($this->need_display_personal_form) {
            return 'dummy.php';
        } else {
            parent::doAction();
        }
    }

    function saveData() {
    }
}
