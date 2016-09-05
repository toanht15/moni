<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class connect extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedLogin = true;
    public $NeedAdminLogin = true;

    public function validate () {
        return true;
    }

    function doAction() {

        //user access denied
        if (isset($this->error)) {
            if ($this->callback_url) {
                return 'redirect: ' . $this->callback_url;
            } else {
                return 'redirect: ' . Util::rewriteUrl('', '', array(), array('connect' => 'gg'));
            }
        }

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
                $_SESSION['connectPath'] = Util::rewriteUrl('google', 'connect');
                $_SESSION['callback_url'] = $this->callback_url;
                $this->ggRedirect($authUrl);
            }
            $this->setSession('connectPath',null);
            $client->authenticate($this->code);

            $channelsResponse = $youtube->channels->listChannels('id', array(
                'mine' => 'true',
            ));

            $this->code = null;
            $this->callback_url = $_SESSION['callback_url'];
            $this->setSession('callback_url',null);
            $userinfo = $plus->userinfo->get();

            $brand_social_account = $brand_social_account_service->getBrandSocialAccount($this->getBrand()->id, $userinfo['id'], $social_app->id);

            $date = date("Y-m-d H:i:s", time());

            // 管理者アカウントをAppAccountsに保存しておく
            if($brand_social_account) {
                $brand_social_account->user_id = $this->getBrandsUsersRelation()->user_id;
                $brand_social_account->social_media_account_id = $userinfo['id'];
                $brand_social_account->social_app_id = SocialApps::PROVIDER_GOOGLE;
                $brand_social_account->token = $client->getAccessToken();
                $brand_social_account->token_secret = json_decode($client->getAccessToken())->refresh_token;
                $brand_social_account->token_expired_flg = BrandSocialAccounts::TOKEN_NOT_EXPIRE;
                $brand_social_account->token_update_at = $date;
                $brand_social_account->name = $userinfo['name'];
                $brand_social_account->screen_name = $userinfo['name'];
                $brand_social_account->picture_url = $userinfo['picture'] . '?sz=200';//画像のサイズを設定する
                $userinfo['channelId'] = $channelsResponse['items'][0]['id'];
                if ($brand_social_account->hidden_flg == 1) {
                    $brand_social_account->order_no = $brand_social_account_service->getMaxOrder($this->getBrand()->id) + 1;
                }
                $brand_social_account->hidden_flg = 0;
                $brand_social_account->store = json_encode($userinfo);
                $brand_social_account->token_expired_flg = BrandSocialAccounts::TOKEN_NOT_EXPIRE;
                $brand_social_account_service->updateBrandSocialAccountAndStream($brand_social_account);
            } else {
                $brand_social_account->user_id = $this->getBrandsUsersRelation()->user_id;
                $brand_social_account->social_media_account_id = $userinfo['id'];
                $brand_social_account->social_app_id = SocialApps::PROVIDER_GOOGLE;
                $brand_social_account->token = $client->getAccessToken();
                $brand_social_account->token_secret = json_decode($client->getAccessToken())->refresh_token;
                $brand_social_account->token_expired_flg = BrandSocialAccounts::TOKEN_NOT_EXPIRE;
                $brand_social_account->order_no = $brand_social_account_service->getMaxOrder($this->getBrand()->id) + 1;
                $brand_social_account->token_update_at = $date;
                $brand_social_account->name = $userinfo['name'];
                $brand_social_account->screen_name = $userinfo['name'];
                $brand_social_account->picture_url = $userinfo['picture'].'?sz=200';
                $userinfo['channelId'] = $channelsResponse['items'][0]['id'];
                $brand_social_account->store = json_encode($userinfo);
                $brand_social_account_service->createBrandSocialAccount($brand_social_account, $this->getBrand());


                // 新規で連携した時は、entryを取得する
                if ($this->callback_url) {
                    $this->getYoutubeVideos($youtube, $brand_social_account);
                }
            }
        }
        //新しいStream保存します

        if ($this->callback_url) {
            return 'redirect: ' . $this->callback_url;
        } else {
            return 'redirect: ' . Util::rewriteUrl('', '', array(), array('connect' => 'gg'));
        }
    }
    public static function ggRedirect($url) {
        echo "<script type='text/javascript'>top.location.href = '$url';</script>";
        exit();
    }

    private function getYoutubeVideos($youtube, BrandSocialAccount $brand_social_account) {
        try {
            /** @var CrawlerService $crawler_service */
            $crawler_service = $this->getService("CrawlerService");
            /** @var YoutubeStreamService $stream_service */
            $stream_service = $this->getService("YoutubeStreamService");

            $stream = $brand_social_account->getYoutubeStream();

            $crawler_url = $crawler_service->getCrawlerUrlByTargetId("youtube_stream_".$stream->id);

            $channelsResponse = $youtube->channels->listChannels('contentDetails', array('mine' => 'true'));

            foreach ($channelsResponse['items'] as $channel) {
                $playlistItems = $stream_service->getYoutubeVideoInfo($channel, $youtube);
                $stream_service->doStore($stream, $crawler_url, $playlistItems, 'pub_date');
            }
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error("api_get_youtube_videos#doAction() Exception crawler_url_id = " . $crawler_url->id);
            aafwLog4phpLogger::getDefaultLogger()->error($e);
        }
    }
}
