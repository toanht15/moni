<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.CpInfoContainer');

abstract class BrandcoAuthActionBase extends BrandcoPOSTActionBase {
    use BrandcoAuthTrait;

    const AUTH_ACTION_LOGIN = 'login';
    const AUTH_ACTION_SIGNUP = 'signup';

    // Action関連の変数
    public $NeedOption = array();
    public $CsrfProtect = true;
    public $isLoginPage = true;
    protected $ContainerName = 'logging';

    // 認証関連の変数
    protected $client_id;
    protected $cp_id;
    protected $page_type;
    protected $redirect_url;
    private $tokens;

    /** @var AdminInviteTokenService $admin_invite_token_service */
    protected $admin_invite_token_service;
    /** @var  UserService $user_service */
    protected $user_service;
    /** @var  BrandsUsersRelationService $brands_users_relation_service */
    protected $brands_users_relation_service;
    /** @var  UserApplicationService $user_application_service */
    protected $user_application_service;
    /** @var  CpUserService $cp_user_service */
    protected $cp_user_service;

    abstract function getAuthAction();

    abstract function doSubAction();

    function doThisFirst() {
        // ログイン関連の値を持つセッションの初期化
        $this->clearLoginSession();

        // グローバル変数の初期化
        $this->initGlobalVariables();

        // 認証関連のサービスクラスの呼び出し
        $this->admin_invite_token_service       = $this->getService('AdminInviteTokenService');
        $this->user_service                     = $this->getService('UserService');
        $this->brands_users_relation_service    = $this->getService('BrandsUsersRelationService');
        $this->user_application_service         = $this->getService('UserApplicationService');
        $this->cp_user_service                  = $this->getService('CpUserService');

        if ($this->hasAdminInviteToken($this->getBrand()->id)) {
            $this->NeedPublic = true;
            $this->redirect_url = Util::rewriteUrl('auth', 'certificate_administrator');
        }
    }

    function beforeValidate() {
        $this->POST['mode'] = $this->getAuthAction();
    }

    function doAction() {
        // 子クラス依存の処理を実行
        $res = $this->doSubAction();

        // ActionFormを初期化
        $this->Data['saved'] = 1;

        // ログイン済み && キャンペーンIDを持っている場合
        if (is_null($res) && $this->cp_id ) {
            // セッションから削除
            $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('cp_id', null);

            // 常設キャンペーン && 未回答の場合
            $cp = CpInfoContainer::getInstance()->getCpById($this->cp_id);
            if ($cp->isNonIncentiveCp() && !$this->getBrandSession('qa')[$cp->id]) {
                $AAIDLoginByLoginTokenUrl =  $this->createAAIDLoginByLoginTokenUrl();
                $this->setSession('directoryName',$this->getBrand()->directory_name);
                $this->setSession('mailLoginFlg',1);
                $this->setSession('authRedirectUrl',$this->redirect_url);
                return 'redirect: ' .$AAIDLoginByLoginTokenUrl;
            }

            // すでに参加済みの場合

            if ($this->cp_user_service->isJoinedCp($cp->id, $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('pl_monipla_userId'))) {

                $AAIDLoginByLoginTokenUrl =  $this->createAAIDLoginByLoginTokenUrl();
                $this->setSession('directoryName',$this->getBrand()->directory_name);
                $this->setSession('mailLoginFlg',1);
                $this->setSession('authRedirectUrl',Util::rewriteUrl('messages', 'thread', array($this->cp_id)));
                return 'redirect: '.$AAIDLoginByLoginTokenUrl;
            }

            // メールアドレスでログイン可能な場合
            if (!$cp->hasJoinLimitSnsWithoutPlatform()) {
                $this->Data['beginner_flg'] = CpUser::NOT_BEGINNER_USER;
                $this->Data['cp_id'] = $cp->id;

                $AAIDLoginByLoginTokenUrl =  $this->createAAIDLoginByLoginTokenUrl();
                $this->setSession('authRedirectUrl',Util::rewriteUrl(
                    'auth',
                    'signup_redirect',
                    array(),
                    array('cp_id'=>$this->Data['cp_id'],'beginner_flg'=>$this->Data['beginner_flg'])
                ));
                $this->setSession('directoryName',$this->getBrand()->directory_name);
                $this->setSession('mailLoginFlg',1);
                return 'redirect: '.$AAIDLoginByLoginTokenUrl;
            }
        }

        // ログイン関連の値をセッションに格納
        $this->resetLoginSession();

        return is_null($res) ? 'redirect: ' . $this->redirect_url : $res;
    }

    public function createAAIDLoginByLoginTokenUrl(){
        /** @var  $redirect_platform_service RedirectPlatformService */
        $redirect_platform_service = $this->createService('RedirectPlatformService');
        $user = $this->user_service->getUserByBrandcoUserId($this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('pl_monipla_userId'));
        $parameters = array(
            'accessToken' => $this->tokens['accessToken'],
            'refreshToken' => $this->tokens['refreshToken'],
            'platformUserId' => $user->monipla_user_id
        );

        $options = array('brand' => $this->getBrand());
        return $redirect_platform_service->getUrlLoginByLoginToken($parameters,$options);
    }

    /**********************************************************************
     * SESSION関連
     **********************************************************************/
    public function clearLoginSession() {
        // TODO setBrandSessionに変更する
        $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('authRedirectUrl', null);
        $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('loginReferer', null);
        $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('clientId', null);
    }

    public function resetLoginSession() {
        if (!$this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('authRedirectUrl')) {
            // TODO setBrandSessionに変更する
            $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('authRedirectUrl', $this->redirect_url);
            $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('loginRedirectUrl', null);
        }
        $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('loginReferer', $_SERVER['HTTP_REFERER']);
        $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('clientId', $this->client_id);
    }

    /**********************************************************************
     * Function
     **********************************************************************/

    public function initGlobalVariables() {
        $this->client_id = ApplicationService::CLIENT_ID_PLATFORM;
        $this->cp_id = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('cp_id');
        $this->page_type = $this->POST['page_type'] ?: 'login';
        $this->redirect_url = $this->getRedirectUrl();

        if ($this->page_type === 'campaign') {
            $this->Form = array(
                'package' => 'campaigns',
                'action' => $this->cp_id
            );
        } elseif ($this->getBrandSession('isCmtPluginMode')) {
            $this->Form = array(
                'package' => 'my',
                'action' => 'login?display=popup'
            );
        } else  {
            $this->Form = array(
                'package' => 'my',
                'action' => 'login?cp_id=' . $this->cp_id
            );
        }
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function hasAdminInviteToken($brand_id) {
        return $this->admin_invite_token_service->getValidInvitedToken($brand_id);
    }

    /**
     * @return string
     */
    public function getRedirectUrl() {
        if ($this->getBrandSession('isCmtPluginMode')) {
            return Util::rewriteUrl('plugin', 'callback');
        }
        return $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('loginRedirectUrl') ?: Util::getBaseUrl();
    }

    /**
     * @return mixed
     */
    public function getTokens() {
        return $this->tokens;
    }

    /**
     * @param mixed $tokens
     */
    public function setTokens($tokens) {
        $this->tokens = $tokens;
    }
    
    
}