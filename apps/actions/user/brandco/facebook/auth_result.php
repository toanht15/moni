<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class auth_result extends BrandcoGETActionBase {

    public $NeedOption = array();
	public $NeedLogin = true;
    /** @var $service BrandSocialAccountService */
    private $service;
    private $session;
    private $facebook_api_client;

    public function beforeValidate() {
        // Facebook認証エラーチェック
        if (isset($this->error_reason) && $this->error_reason == 'user_denied') {
            return 'redirect: /'. $this->getBrand()->directory_name . '?mid=facebook-auth-failed';
        }
        // provider情報取得
        $social_app = $this->createService('SocialAppService')->getSocialAppByProvider(SocialApps::PROVIDER_FACEBOOK, 1, true);
        // ユーザ情報取得
        $loginInfo = $this->getLoginInfo();
        // チェック
        if (!$loginInfo || !$this->GET['mode'] || !$social_app) return 'redirect: /'. $this->getBrand()->directory_name;
        // Facebook認証用情報設定
        $this->buildFacebookAuthInfo($this->GET);

        $this->service = $this->createService('BrandSocialAccountService');
    }

	public function validate () {
		return true;
	}

	public function doAction() {
        // extend token
        if ($this->GET['mode'] == 'extend'){
            try {
                // Facebook page admin check
                if (!$this->service->isFacebookPageAdmin($this->session->getToken(), $this->page_id)) {
                    return 'redirect: /' . $this->getBrand()->directory_name . '?mid=facebook-no-page-admin';
                }

                // token更新
                $result = $this->facebook_api_client->getLongAccessToken($this->session->getToken());
                $brand_social_account = $this->service->getBrandSocialAccount($this->getBrand()->id, $this->page_id, SocialApps::PROVIDER_FACEBOOK, 0, 0);
                $brand_social_account->token = $result['access_token'];
                $brand_social_account->token_update_at = date('Y-m-d H:i:s');
                $this->service->updateBrandSocialAccount($brand_social_account);
                return 'redirect: /'. $this->brand->directory_name . '?mid=updated';
            } catch( Exception $ex ) {
                return 'redirect: /'. $this->brand->directory_name . '?mid=failed';
            }
        }
        return 'redirect: /'. $this->getBrand()->directory_name;
    }

    /**
     * Facebook認証用情報設定
     * @param $params
     */
    private function buildFacebookAuthInfo($params) {
        $callback_url = Util::rewriteUrl('facebook', 'auth_result', array(), array('mode'=> $params['mode'],'page_id'=> $params['page_id']));
        $this->facebook_api_client = $this->getFacebook();
        $this->facebook_api_client->setRedirectLoginHelper($callback_url);
        $this->session = $this->facebook_api_client->getSessionFromRedirect();
    }
}
