<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.exception.EntityNotFoundException');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class connect extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedLogin = true;
    protected $ContainerName = 'login_outer';

    public function doThisFirst() {
        // Brandのdirectory_nameを取得
        $brand_service = $this->createService('BrandService');
        $brand = $brand_service->getBrandByOuterToken($this->GET['token']);
        if (!$brand) {
            return 403;
        }
        $this->GET['directory_name'] = $brand->directory_name;
        BrandInfoContainer::getInstance()->initialize($brand);
    }

    public function validate () {
        // 認証チェック
        if ($this->getSession('login_outer') !== 1) {
            return false;
        }

        $this->NeedPublic = true;
        return true;
    }

    function doAction() {
        $social_app = $this->createService('SocialAppService')->getSocialAppByProvider(SocialApps::PROVIDER_GOOGLE, 1, true);

        if ($social_app) {
            //admin権限をセット
            $this->setMode( self::BRANDCO_MODE_ADMIN );
            /** @var BrandSocialAccountService $brand_social_account_service */
            $brand_social_account_service = $this->createService('BrandSocialAccountService');
            $client = $this->getGoogle();
            $plus = new Google_Oauth2Service($client);
            $youtube = new Google_YouTubeService($client);
            if(!$this->code){
                $authUrl = $client->createAuthUrl();
                $_SESSION['connectPath'] = Util::rewriteUrl(
                    'google_outer',
                    'connect',
                    [],
                    ['token' => $this->GET['token']]
                );
                $this->ggRedirect($authUrl);
            }
            $this->setSession('connectPath', null);
            $client->authenticate($this->code);

            $channelsResponse = $youtube->channels->listChannels('id', array(
                'mine' => 'true',
            ));

            $this->code = null;
            $userinfo = $plus->userinfo->get();

            $brand_social_account = $brand_social_account_service->getBrandSocialAccount($this->getBrand()->id, $userinfo['id'], $social_app->id);

            if (!$brand_social_account) {
                $brand_social_account = $brand_social_account_service->getHiddenBrandSocialAccountByAppId($userinfo['id'], $social_app->id);
            }

            $date = date("Y-m-d H:i:s", time());

            // user_idの取得
            $brand_outer_token_service = $this->createService('BrandOuterTokenService');
            $brand_outer_token = $brand_outer_token_service->getBrandOuterTokenByToken($this->GET['token']);
            if (!$brand_outer_token) {
                $e = new EntityNotFoundException(
                    'brand_outer_token record not found. token=' . $this->GET['token']
                );
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->error($e);
                return 'redirect: ' . Util::rewriteUrl(
                    'sns',
                    'login_outer_form',
                    [],
                    ['token' => $this->GET['token']]
                );
            }

            // 管理者アカウントをAppAccountsに保存しておく
            if ($brand_social_account) {
                $brand_social_account->user_id = $brand_outer_token->user_id;
                $brand_social_account->social_media_account_id = $userinfo['id'];
                $brand_social_account->social_app_id = SocialApps::PROVIDER_GOOGLE;
                $brand_social_account->token = $client->getAccessToken();
                $brand_social_account->token_secret = json_decode($client->getAccessToken())->refresh_token;
                $brand_social_account->token_update_at = $date;
                $brand_social_account->name = $userinfo['name'];
                $brand_social_account->screen_name = $userinfo['name'];
                $brand_social_account->picture_url = $userinfo['picture'].'?sz=200';//画像のサイズを設定する
                $userinfo['channelId'] = $channelsResponse['items'][0]['id'];
                if ($brand_social_account->hidden_flg == 1) {
                    $brand_social_account->order_no = $brand_social_account_service->getMaxOrder($this->getBrand()->id) + 1;
                }
                $brand_social_account->hidden_flg = 0;
                $brand_social_account->store = json_encode($userinfo);
                $brand_social_account_service->updateBrandSocialAccountAndStream($brand_social_account);
            } else {
                $brand_social_account = $brand_social_account_service->createEmptyBrandSocialAccount();
                $brand_social_account->user_id = $brand_outer_token->user_id;
                $brand_social_account->social_media_account_id = $userinfo['id'];
                $brand_social_account->social_app_id = SocialApps::PROVIDER_GOOGLE;
                $brand_social_account->token = $client->getAccessToken();
                $brand_social_account->token_secret = json_decode($client->getAccessToken())->refresh_token;
                $brand_social_account->order_no = $brand_social_account_service->getMaxOrder($this->getBrand()->id) + 1;
                $brand_social_account->token_update_at = $date;
                $brand_social_account->name = $userinfo['name'];
                $brand_social_account->screen_name = $userinfo['name'];
                $brand_social_account->picture_url = $userinfo['picture'].'?sz=200';
                $userinfo['channelId'] = $channelsResponse['items'][0]['id'];
                $brand_social_account->store = json_encode($userinfo);
                $brand_social_account_service->createBrandSocialAccount($brand_social_account, $this->getBrand());
            }
        }

        $this->setSession('brand_social_account_id', null);
        $this->setSession('brand_social_account_id', $brand_social_account->id);

        return 'redirect: ' . Util::rewriteUrl(
            'google_outer',
            'connect_finish',
            [],
            ['token' => $this->GET['token']]
        );
    }
    public static function ggRedirect($url) {
        echo "<script type='text/javascript'>top.location.href = '$url';</script>";
        exit();
    }
}
