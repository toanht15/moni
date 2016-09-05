<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class login extends BrandcoGETActionBase {
    public $NeedOption = array();
    protected $ContainerName = 'logging';
    public $isLoginPage = true;

    /** @var  AdminInviteTokenService $admin_invite_token_service */
    private $admin_invite_token_service;
    private $cp_id;
    private $invite_token;
    private $msg_token;
    private $is_cmt_plugin_mode;

    public function doThisFirst() {
        // サービスの呼び出し
        $this->admin_invite_token_service = $this->getService('AdminInviteTokenService');

        // 変数に値を格納する
        $this->cp_id = $this->GET['cp_id'] ?: null;
        $this->msg_token = $this->GET['msg_token'] ?: null;
        $this->is_cmt_plugin_mode = $this->isCmtPluginMode();

        // Reset comment data session if this is default login mode
        if (!$this->is_cmt_plugin_mode) {
            $this->setBrandSession('commentData', null);
        }

        // セッションをリセット
        $this->setSession('cp_id', null);
        $this->setBrandSession('isCmtPluginMode', null);

        if (!$this->getBrandsUsersRelation() || !$this->getBrandsUsersRelation()->isAdmin()) {
            if ($this->GET['invite_token']) {
                // Sessionに値を格納する
                $this->setSession('invite_token' . $this->getBrand()->id, $this->GET['invite_token']);
            }

            $this->invite_token = $this->getValidInviteToken();
            // invite tokenが正しい場合、ページを公開する
            if ($this->invite_token) {
                $this->NeedPublic = true;
            }
        }
    }


    public function validate() {
        return true;
    }

    public function doAction() {
        // 変数に値を格納する
        $this->Data['pageInfo'] = array(
            'loginRedirectUrl' => $this->getLoginRedirectUrl(),
        );
        $this->Data['pageStatus']['og'] = array(
            'title' => 'ログイン - ' . $this->getBrand()->name
        );
        if ($this->cp_id) {
            $this->Data['pageInfo']['cp_id'] = $this->cp_id;

            // Sessionに値を格納する
            $this->setSession('cp_id', $this->cp_id);
        }

        if ($this->is_cmt_plugin_mode) {
            $this->setBrandSession('isCmtPluginMode', $this->is_cmt_plugin_mode);
        }

        $this->Data['loggingFormInfo'] = array();
        $this->Data['loggingFormInfo']['page_type'] = 'login';
        $this->Data['loggingFormInfo']['template_file'] = 'auth/PreLoginForm.php';
        // 一度ログイン or サインアップエラーしているとき
        if ($this->Data['ActionError']) {
            if ($this->Data['ActionForm']['mode'] === 'signup') {
                $this->Data['loggingFormInfo']['template_file'] = 'auth/SignupForm.php';
            } else if ($this->Data['ActionForm']['mode'] === 'login') {
                $this->Data['loggingFormInfo']['template_file'] = 'auth/LoginForm.php';
            }

            // msgTokenがSessionにあれば取得する
            $this->msg_token = $this->getBrandSession('msgToken');
        } else {
            $this->setBrandSession('msgToken', null);
        }

        // メッセージトークンを持っている場合: ログイン可能なSNSを絞る
        if ($this->msg_token) {
            $this->setBrandSession('msgToken', $this->msg_token);
            $this->setLoggingFormInfoForMsgLogin($this->msg_token);
        }

        // 管理者トークンを持っている場合
        if ($this->invite_token) {
            if ($this->getBrandsUsersRelation() && $this->getBrandsUsersRelation()->isAdmin()) {
                // ワンタイムトークンの削除処理
                $this->admin_invite_token_service->certificatedToken($this->getBrand()->id, $this->invite_token);
                $this->setSession('invite_token' . $this->getBrand()->id, null);

                return 'redirect:' . Util::rewriteUrl('','');
            }

            if ($this->isLogin() && $this->admin_invite_token_service->canUseInviteToken($this->invite_token)) {
                return 'redirect:' . Util::rewriteUrl('auth', 'certificate_administrator');
            }

            return 'user/brandco/my/invited_logging.php';
        }

        // ログイン済みなら
        if ($this->isLogin()) {
            if ($this->cp_id) {
                /** @var CpFlowService $cp_flow_service */
                $cp_flow_service = $this->getService('CpFlowService');
                $cp = $cp_flow_service->getCpById($this->cp_id);

                // Auto join AU campaign
                if ($cp->isAuCampaign()) {
                    $this->Data['cp_id'] = $this->cp_id;
                    $this->Data['beginner_flg'] = CpUser::NOT_BEGINNER_USER;

                    return 'user/brandco/auth/signup_redirect.php';
                }

                return 'redirect: ' . Util::rewriteUrl('', 'campaigns', array($this->cp_id));
            }

            // Auto redirect to NeedRedirect page
            if ($this->Data['pageInfo']['loginRedirectUrl']) {
                if (DEBUG) {
                    aafwLog4phpLogger::getDefaultLogger()->debug("redirecting... session: " . json_encode($this->SESSION, JSON_PRETTY_PRINT));
                }
                return 'redirect: ' . $this->Data['pageInfo']['loginRedirectUrl'];
            }

            // Redirect to TOP by default
            return 'redirect: ' . Util::rewriteUrl('', '');
        }

        if ($this->is_cmt_plugin_mode) {
            $this->Data['pageStatus']['is_cmt_plugin_mode'] = $this->is_cmt_plugin_mode;
            return 'user/brandco/my/popup_logging.php';
        }

        return 'user/brandco/my/logging.php';
    }

    /**
     * @return null
     */
    public function getValidInviteToken() {
        return $this->admin_invite_token_service->getValidInvitedToken($this->getBrand()->id, $this->GET['invite_token']);
    }

    /**
     * @return null|string
     */
    public function getLoginRedirectUrl() {
        $login_redirect_url = $this->getSession('loginRedirectUrl');
        if (!$login_redirect_url) {
            return $login_redirect_url;
        }

        $parsed_url = parse_url($login_redirect_url);

        $mapped_brand_id = Util::getMappedBrandId($parsed_url['host']);
        if ($mapped_brand_id != Util::NOT_MAPPED_BRAND) {
            if ($this->getBrand()->id != $mapped_brand_id) {
                return Util::getBaseUrl();
            }
        } else {
            $parsed_request_uri = Util::parseRequestUri($parsed_url['path']);
            if ($parsed_request_uri['directory_name'] && $parsed_request_uri['directory_name'] != $this->getBrand()->directory_name) {
                return Util::getBaseUrl();
            }
        }

        return $login_redirect_url;
    }

    /**
     * @param $msg_token
     */
    public function setLoggingFormInfoForMsgLogin($msg_token) {
        $params = json_decode(base64_decode($msg_token), true);
        if ($params['user_id'] && $params['cp_action_id']) {
            /** @var UserService $user_service */
            $user_service = $this->getService('UserService');
            /** @var BrandcoAuthService $brandco_auth_service */
            $brandco_auth_service = $this->getService('BrandcoAuthService');

            $user = $user_service->getUserByBrandcoUserId($params['user_id']);
            $userInfo = $brandco_auth_service->getUserInfoByQuery($user->monipla_user_id);
            if ($user && $userInfo) {
                $this->Data['loggingFormInfo']['sns_limited'] = true;
                $this->Data['loggingFormInfo']['preset_mail_address'] = $userInfo->mailAddress;
                $this->Data['loggingFormInfo']['available_sns_accounts'] = array();
                $social_accounts = $this->getSocialAccountsByUserId($params['user_id']);
                foreach ($social_accounts as $social_account) {
                    $this->Data['loggingFormInfo']['available_sns_accounts'][] = $social_account->social_media_id;
                }
            }
        }
    }

    /**
     * @param $user_id
     * @return aafwEntityContainer|array
     */
    public function getSocialAccountsByUserId($user_id) {
        /** @var SocialAccountService $social_account_service */
        $social_account_service = $this->getService('SocialAccountService');
        return $social_account_service->getSocialAccountsByUserIdOrderBySocialMediaAccountId($user_id);
    }

    /**
     * @return bool
     */
    public function isCmtPluginMode() {
        if (!$this->hasOption(BrandOptions::OPTION_COMMENT, false)) {
            return false;
        }

        if (Util::isNullOrEmpty($this->GET['display']) || $this->GET['display'] != 'popup') {
            return false;
        }

        $comment_data = $this->getBrandSession('commentData');
        if (Util::isNullOrEmpty($comment_data)) {
            return false;
        }

        if (Util::isNullOrEmpty($comment_data['comment_plugin_id'])) {
            return false;
        }

        if ($comment_data['object_type'] == CommentUserRelation::OBJECT_TYPE_REPLY && Util::isNullOrEmpty($comment_data['comment_user_id'])) {
            return false;
        }

        return true;
    }
}
