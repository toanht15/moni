<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class auth extends BrandcoGETActionBase {

    public $NeedOption = array();
	public $NeedLogin = true;
    /** @var BrandSocialAccountService $service */
    protected $service;
    private $facebook_api_client;

	public function validate() {
		return true;
	}

	public function doAction() {
        $social_app = $this->createService('SocialAppService')->getSocialAppByProvider(SocialApps::PROVIDER_FACEBOOK, 1, true);
        if (!$this->GET['mode'] || !$social_app) return 'redirect: /' . $this->getBrand()->directory_name;

        $this->facebook_api_client = $this->getFacebook();

        // AccessToken延長
        if ($this->GET['mode'] == 'extend' && $this->page_id) {
            $callback_url = Util::rewriteUrl('facebook', 'auth_result', array(), array('mode' => $this->GET['mode'],'page_id' => $this->page_id));
            // redirect
            $this->facebook_api_client->setRedirectLoginHelper($callback_url);
            $this->facebook_api_client->fbRedirectLogin();
        }

        return 'redirect: /'. $this->getBrand()->directory_name;
    }
}
