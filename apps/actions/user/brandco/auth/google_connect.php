<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class google_connect extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedLogin = true;

    public function validate () {
        return true;
    }

    function doAction() {
        /** @var SocialAppService $social_app_service */
        $social_app_service = $this->getService('SocialAppService');
        $social_app = $social_app_service->getSocialAppByProvider(SocialApps::PROVIDER_GOOGLE, 1, true);

        if ($social_app) {

            $user_id = $this->getBrandsUsersRelation()->user_id;

            // OAuth認証
            $client = $this->getGoogleUser();
            $plus = new Google_Service_Oauth2($client);

            if (!$this->GET['code']) {
                if ($this->cp_action_id) {
                    $this->setSession('cp_action_'.$this->cp_action_id, array('autoFollow' => 1));
                }
                $authUrl = $client->createAuthUrl();
                $this->setSession('connectPath', Util::rewriteUrl('auth', 'google_connect'));
                $this->setSession('callback_url', $this->callback_url);
                return 'redirect: ' . $authUrl;
            }

            $this->setSession('connectPath', null);
            $client->authenticate($this->GET['code']);
            $this->GET['code'] = null;
            $user_info = $plus->userinfo->get();

            // 取得情報の保存
            /** @var BrandcoSocialAccountService $brc_social_account_service */
            $brc_social_account_service = $this->getService('BrandcoSocialAccountService');
            $brc_social_account = $brc_social_account_service->getBrandcoSocialAccount($user_id, $social_app->id);
            if (!$brc_social_account) {
                $brc_social_account = $brc_social_account_service->createEmptyBrandcoSocialAccount();
                $brc_social_account->user_id = $user_id;
                $brc_social_account->social_app_id = $social_app->id;
            }
            $brc_social_account->social_media_account_id = $user_info['id'];
            $brc_social_account->access_token            = $client->getAccessToken();
            $brc_social_account->refresh_token           = $client->getRefreshToken();
            $brc_social_account->token_update_at         = date("Y-m-d H:i:s", time());
            $brc_social_account->store                   = json_encode($user_info);
            $brc_social_account_service->saveBrandcoSocialAccount($brc_social_account);
        }

        $callback_url = $this->getSession('callback_url');
        $this->setSession('callback_url', null);
        return 'redirect: ' . ($callback_url ? : Util::rewriteUrl('', ''));
    }
}
