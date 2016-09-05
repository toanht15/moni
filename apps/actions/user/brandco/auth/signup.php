<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.BrandInfoContainer');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.classes.core.UserAttributeManager');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');
AAFW::import('jp.aainc.classes.services.merge.AccountMergeUtil');

class signup extends BrandcoGETActionBase {
    use BrandcoAuthTrait;

    // Action関連の変数
    public $NeedOption = array();
    public $isLoginPage = true;
    protected $ContainerName = 'signup';

    // 認証関連の変数
    protected $client_id;
    protected $cp_id;
    protected $redirect_url;
    protected $invite_token;
    protected $is_cmt_plugin_mode;

    // ロガー
    protected $logger;

    /** @var  AdminInviteTokenService $admin_invite_service */
    private $admin_invite_token_service;
    /** @var  CpUserService $cp_user_service */
    private $cp_user_service;
    /** @var  SocialAccountService $social_account_service */
    private $social_account_service;
    /** @var  UserApplicationService $user_application_service */
    private $user_application_service;

    public function doThisFirst() {
        // セッション削除
        $this->setSession('directoryName', null);

        // グローバル変数にセッション値を格納
        $this->client_id        = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('clientId') ?: ApplicationService::CLIENT_ID_PLATFORM;
        $this->cp_id            = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('cp_id') ?: null;
        $this->redirect_url     = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('authRedirectUrl') ?: Util::rewriteUrl('', '');
        $this->is_cmt_plugin_mode       = $this->getBrandSession('isCmtPluginMode') ?: null;
        $this->invite_token     = null;

        // サービスクラスの呼び出し
        $this->admin_invite_token_service   = $this->getService('AdminInviteTokenService');
        $this->cp_user_service              = $this->getService('CpUserService');
        $this->social_account_service       = $this->getService('SocialAccountService');
        $this->user_application_service     = $this->getService('UserApplicationService');

        // ロガーの呼び出し
        $this->logger = aafwLog4phpLogger::getDefaultLogger();

        if ($this->hasAdminInviteToken($this->getBrand()->id)) {
            $this->NeedPublic  = true;
        }
    }

    public function beforeValidate () {
        $this->resetValidateError();
    }

    public function validate() {
        return true;
    }

    function doAction() {
        // ログイン済みの場合はリダイレクト
        if ($this->isLogin()) {
            //redisにアカウントマージ用のリダイレクトURLが入っていたらそちらに飛ばす
            $userId = $this->getSession('pl_monipla_userId');
            $user = $this->getModel(Users::class)->findOne($userId);
            $mergeRedirectUrl = AccountMergeUtil::getAutRedirectUrlFromRedis($user->monipla_user_id);
            if($mergeRedirectUrl){
                AccountMergeUtil::delAuthRedirectUrlFromRedis($user->monipla_user_id);
                //ドメインマッピングの企業からだと、ドメインが違うせいでログインセッションからユーザー情報とれないのでredisに入れてます
                AccountMergeUtil::setAlliedIdToRedis($user->monipla_user_id);
                return 'redirect: '.$mergeRedirectUrl;
            }
            return 'redirect: ' . $this->redirect_url;
        }

        try {
            // AccessTokenを取得し、SESSIONに保存
            list($accessToken, $refreshToken) = $this->getTokensByCode($this->GET['code']);
            if (!$accessToken) {
                $query_params = $this->is_cmt_plugin_mode ? array('display', 'popup') : array();
                return 'redirect: ' . Util::rewriteUrl('my', 'login', array(), $query_params);
            }

            // userInfoを取得
            $userInfo = $this->getUserInfoByTokens($accessToken, $refreshToken);
            if (!$userInfo) {
                return '500';
            }

            // TODO AccessTokenの持ち方を考える
            // $codeがnullの場合、Userが存在するか確認 (存在しない場合はaccessToken、refreshTokenを削除する)
            if (!$this->GET['code'] && !$this->Data['ActionError']) {
                $user = $this->getUserService()->getUserByMoniplaUserId($userInfo->id);
                if (!$user) {
                    $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('accessToken', null);
                    $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('refreshToken', null);
                    return '404';
                }
            }

            // キャンペーンIDを持っていた場合
            if ($this->cp_id) {
                // セッションから削除
                // TODO setBrandSessionに変更する
                $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('cp_id', null);

                $cp = CpInfoContainer::getInstance()->getCpById($this->cp_id);

                // キャンペーンIDが正しいか確認
                // TODO 限定SNS以外でのログインを防ぐ
                if ($cp->brand_id === $this->getBrand()->id) {
                    // 常設キャンペーン && 未回答の場合
                    if ($cp->isNonIncentiveCp() && !$this->getBrandSession('qa')[$cp->id]) {
                        return 'redirect: ' . Util::rewriteUrl('', 'campaigns', array($cp->id));
                    }

                    $user_transaction = aafwEntityStoreFactory::create('Users');

                    try {
                        $user_transaction->begin();

                        // User (PreUser) の作成
                        $user = $this->getOrCreateMoniplaUser($userInfo, true, $this->getBrand()->app_id, $accessToken, $refreshToken, $this->client_id);

                        $required_personal_form = $this->isPersonalFormRequired();

                        // BrandsUsersRelationの作成
                        $brands_users_relation = $this->getOrCreateBrandUserRelation($this->getBrand()->id, $user->id, $required_personal_form ? BrandsUsersRelation::SIGNUP_WITHOUT_INFO : BrandsUsersRelation::SIGNUP_WITH_INFO);

                        $this->login($userInfo, $brands_users_relation);

                        $user_transaction->commit();

                        // すでに参加済みの場合

                        if( $this->cp_user_service->isJoinedCp($cp->id, $user->id) ) {
                            //メールアドレスログイン経由で来た場合はAAIDへログインセッション書きに行く
                            if( $this->GET['mail_login'] == 'true' ) {
                                $AAIDLoginByLoginTokenUrl = $this->createAAIDLoginByLoginTokenUrl($accessToken,$refreshToken,$userInfo->id);
                                $this->setSession(
                                    'authRedirectUrl',
                                    Util::rewriteUrl('messages', 'thread', array($this->cp_id))
                                );
                                $this->setSession('directoryName', $this->getBrand()->directory_name);
                                $this->setSession('mailLoginFlg', 1);
                                return 'redirect :' . $AAIDLoginByLoginTokenUrl;
                            }
                            return 'redirect: ' . Util::rewriteUrl('messages', 'thread', array($this->cp_id));
                        }

                        // オプトイン情報 (デフォルトは0) を保存
                        // TODO 参加者が全部Optoutされる仕様なので外した
                        // $this->updateOptin($userInfo->id, UserManager::OPT_OUT);

                        $this->Data['beginner_flg'] = $brands_users_relation->created_at ? CpUser::NOT_BEGINNER_USER : CpUser::BEGINNER_USER;
                        $this->Data['cp_id'] = $this->cp_id;

                        //メールアドレスログイン経由で来た場合はAAIDへログインセッション書きに行く
                        if( $this->GET['mail_login'] == 'true' ) {
                            $AAIDLoginByLoginTokenUrl = $this->createAAIDLoginByLoginTokenUrl($accessToken,$refreshToken,$userInfo->id);
                            $this->setSession(
                                'authRedirectUrl',
                                Util::rewriteUrl(
                                    'auth',
                                    'signup_redirect',
                                    array(),
                                    array(
                                        'cp_id' => $this->Data['cp_id'],
                                        'beginner_flg' => $this->Data['beginner_flg']
                                    )
                                )
                            );
                            $this->setSession('directoryName', $this->getBrand()->directory_name);
                            $this->setSession('mailLoginFlg', 1);
                            return 'redirect :' . $AAIDLoginByLoginTokenUrl;
                        }
                        return 'user/brandco/auth/signup_redirect.php';
                    } catch (Exception $e) {
                        $user_transaction->rollback();
                        $this->logger->error($e->getMessage());
                        $this->logger->error($e);
                        throw $e;
                    }
                }
            }

            return $this->signupByDefault($userInfo, $accessToken, $refreshToken);
        } catch (Exception $e) {
            $this->logger->error('redirect#doAction error clientId = ' . $this->client_id);
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

    public function createAAIDLoginByLoginTokenUrl($accessToken,$refreshToken,$platformUserId){
        $parameters = array(
            'accessToken' =>$accessToken,
            'refreshToken' =>$refreshToken, 
            'platformUserId' =>$platformUserId 
        );
        $options = array('brand' => $this->getBrand());
        /** @var  $redirect_platform_service RedirectPlatformService */
        $redirect_platform_service = $this->createService('RedirectPlatformService');
        return $redirect_platform_service->getUrlLoginByLoginToken($parameters,$options);
    }

    private function getPreData($userInfo) {
        $userAttributeManager = new UserAttributeManager($userInfo, $this->getMoniplaCore());
        $shippingAddressManager = new ShippingAddressManager($userInfo, $this->getMoniplaCore());
        $preData = array();

        $birthday = $userAttributeManager->getBirthDay();
        $preData['birthDay_y'] = date('Y', strtotime($birthday));
        $preData['birthDay_m'] = date('n', strtotime($birthday));
        $preData['birthDay_d'] = date('j', strtotime($birthday));
        $preData['sex'] = $userAttributeManager->getSex();

        $shippingAddress = $shippingAddressManager->getShippingAddress();
        foreach ($shippingAddress as $key => $value) {
            $preData[$key] = $value;
        }

        return $preData;
    }

    /**
     * @param $userInfo
     * @param $accessToken
     * @param $refreshToken
     * @return string
     * @throws aafwException
     */
    private function signupByDefault($userInfo, $accessToken, $refreshToken) {
        // ユーザ情報の取得
        $user = $this->getUserService()->getUserByMoniplaUserId($userInfo->id);

        //マージからのログインの場合は、pl_monipla_userInfoにだけ情報書き込んでマージ画面へ返します
        $mergeRedirectUrl = AccountMergeUtil::getAutRedirectUrlFromRedis($userInfo->id);
        if($mergeRedirectUrl){
            AccountMergeUtil::delAuthRedirectUrlFromRedis($userInfo->id);
            //ドメインマッピングの企業からだと、ドメインが違うせいでログインセッションからユーザー情報とれないのでredisに入れてます
            AccountMergeUtil::setAlliedIdToRedis($userInfo->id);
            session_regenerate_id(true);
            $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('pl_monipla_userInfo', $this->getBrandcoAuthService()->castSocialAccounts($userInfo));
            $redisPersistentSession = new RedisPersistentSession();
            // ログイン成功でトークンリセット
            $redisPersistentSession->setSessionId(session_id(), true);
            return 'redirect: '.$mergeRedirectUrl;
        }
        if (!$user || $user->provisional_flg || !$userInfo->mailAddress) {
            $userManager = new UserManager($userInfo, $this->getMoniplaCore());
            $this->assign('ActionForm', array(
                'name' => $userInfo->name,
                'mail_address' => $userInfo->mailAddress ?: $userManager->getMailAddressCandidate()
            ));
            $this->Data['template_name'] = 'UserProfileForm';

            // 値をセット
            $this->Data['pageStatus']['brand'] = $this->getBrand();
            $this->Data['pageStatus']['is_whitelist'] = $this->getBrand()->isDisallowedBrand();
        } else {
            // BrandsUsersRelationの取得
            $brands_users_relation = $this->getBrandsUsersRelationService()->getBrandsUsersRelation($this->getBrand()->id, $user->id);

            $required_personal_form = $this->isPersonalFormRequired();

            if ($this->is_cmt_plugin_mode) {
                /** @var CommentPluginService $comment_plugin_service */
                $comment_plugin_service = $this->getService('CommentPluginService');
                $comment_data = $this->getBrandSession('commentData');
                $has_fb_public_actions = $this->hasFBPublishActions($userInfo, $this->getBrand()->app_id, $accessToken);
                $share_sns_list = $comment_plugin_service->getUserShareSNSList($comment_data['comment_plugin_id'], $userInfo, $has_fb_public_actions);

                $this->Data['pageStatus']['userInfo'] = $userInfo;
                $this->Data['pageStatus']['commentData'] = $comment_data;
                $this->Data['pageStatus']['share_sns_list'] = $share_sns_list;
            }

            if ((!$brands_users_relation || $brands_users_relation->isProfileQuestionRequired()) && $required_personal_form) {
                // フォームデータ取得
                $this->assign('ActionForm', $this->getPreData($userInfo));

                $this->Data['template_name'] = 'BrandcoSignupForm';
            } elseif ($this->is_cmt_plugin_mode) {
                $this->Data['template_name'] = 'CommentContentForm';
            } else {
                // BrandsUsersRelationの取得 (作成)
                $brands_users_relation = $this->getOrCreateBrandUserRelation($this->getBrand()->id, $user->id, BrandsUsersRelation::SIGNUP_WITH_INFO);

                $this->createOrUpdateUserApplicationAndSocialAccounts($userInfo, $user->id, $this->getBrand()->app_id, $accessToken, $refreshToken, $this->client_id);

                // ログイン処理
                $this->login($userInfo, $brands_users_relation);
                
                if ($this->hasAdminInviteToken($this->getBrand()->id)) {
                    $this->redirect_url = Util::rewriteUrl('auth', 'certificate_administrator');
                } else if (!$brands_users_relation->created_at) { // created_atがない場合はBrandsUsersRelationを新規作成した時
                    $parsed_url = parse_url($this->redirect_url);
                    $parsed_url['query'] = isset($parsed_url['query']) ? $parsed_url['query'] . '&tid=signup_complete' : 'tid=signup_complete';
                    $this->redirect_url = $this->unparse_url($parsed_url);
                }


                return 'redirect: ' . $this->redirect_url;
            }
        }

        return $this->getReturnView();
    }

    public function getReturnView() {
        if ($this->is_cmt_plugin_mode) {
            $this->Data['pageStatus']['is_cmt_plugin_mode'] = $this->is_cmt_plugin_mode;
            $returnView = 'popup_signup';
        } else {
            $returnView = $this->invite_token ? 'invited_signup' : 'signup';
        }

        return sprintf('user/brandco/auth/%s.php', $returnView);
    }

    /**
     * @param $code
     * @return array
     */
    private function getTokensByCode($code) {
        if ($code) {
            // AccessTokenの取得
            $result = $this->createAccessTokenByCodeAndClientId($code, $this->client_id);
            if ($result || $result['accessToken']) {
                // AccessTokenをセッションに格納
                $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('accessToken', $result['accessToken']);
                $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('refreshToken', $result['refreshToken']);
            }
        }

        return array(
            $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('accessToken'),
            $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('refreshToken')
        );
    }

    /**
     * @param $accessToken
     * @param $refreshToken
     * @return bool|null
     */
    private function getUserInfoByTokens($accessToken, $refreshToken) {
        // AccessTokenからUserIdを取得
        $userInfo = $this->getUserInfoByAccessToken($accessToken);
        if (!$userInfo || !$userInfo->id) {
            $this->logger->error("Thrift api error: can't get 'user info'. (access_token = {$accessToken})");
            return false;
        }

        if (!$userInfo->mailAddress) {
            // userIdからuserInfoを取得
            $userInfo = $this->getUserInfoByMoniplaUserId($userInfo->id);
            if (!$userInfo || !$userInfo->id) {
                $this->logger->error("Thrift api error: can't get 'user info'. (monipla_user_id = {$userInfo->id})");
                return false;
            }
        }

        return $userInfo;
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function hasAdminInviteToken($brand_id) {
        $invite_token =  $this->admin_invite_token_service->getValidInvitedToken($brand_id);
        if ($invite_token) {
            $this->invite_token = $invite_token;
            return true;
        }

        return false;
    }
}
