<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.entities.BrandsUsersRelation');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');

class popup_signup_post extends BrandcoPOSTActionBase {
    use BrandcoAuthTrait;

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);
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

    public function doThisFirst() {
        // グローバル変数にセッション値を格納
        $this->redirect_url = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('authRedirectUrl') ?: Util::rewriteUrl('', '');
        $this->client_id    = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('clientId');
        $this->accessToken  = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('accessToken');
        $this->refreshToken = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('refreshToken');

        // UserInfoの取得
        if ($this->accessToken && $this->refreshToken) {
            $this->userInfo = $this->getUserInfoByAccessToken($this->accessToken);
        } else {
            throw new Exception("Error: The 'access token' is null");
        }
    }

    public function validate () {
        return true;
    }

    function doAction() {
        // AccessTokenからUserInfoを取得
        $userInfo = $this->getUserInfoByMoniplaUserId($this->userInfo->id);
        if (!$userInfo) {
            $this->logger->error("Thrift api error: can't get 'user info'. (monipla_user_id = {$this->userInfo->id})");
            return '500';
        }

        // Comment Plugin - save comment data to session
        $comment_data = $this->getBrandSession('commentData');
        $comment_data['social_media_ids'] = $this->POST['social_media_ids'];
        $this->setBrandSession('commentData', $comment_data);

        // Userの取得
        $user = $this->getUserService()->getUserByMoniplaUserId($userInfo->id);
        // BrandsUsersRelationの取得 (作成)
        $brands_users_relation = $this->getOrCreateBrandUserRelation($this->getBrand()->id, $user->id, BrandsUsersRelation::SIGNUP_WITH_INFO);

        $this->createOrUpdateUserApplicationAndSocialAccounts($userInfo, $user->id, $this->getBrand()->app_id, $this->accessToken, $this->refreshToken, $this->client_id);

        // ログイン処理
        $this->login($userInfo, $brands_users_relation);
        return 'redirect: ' . $this->redirect_url;
    }
}