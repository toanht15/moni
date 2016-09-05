<?php

AAFW::import('jp.aainc.classes.task.CrawlerTask');

class UpdateSocialAccountInfoTask extends CrawlerTask {

	public function __construct($crawler_type) {
		$this->service_factory = new aafwServiceFactory ();
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
		$this->config = aafwApplicationConfig::getInstance();
	}

	public function prepare() {}

	public function crawl() {

		$this->updateFBPageInfo();

        $this->updateTWAccountInfo();

        $this->updateYTChannelInfo();

        $this->updateIGAccountInfo();
	}

    /**
     * Facebookページの情報を更新します。
     */
    private function updateFBPageInfo() {
        $facebook_client = new FacebookApiClient();
        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');
        $brand_social_accounts = $brand_social_account_service->getBrandsSocialAccountsBySocialAppIdAndExpiredFlg(SocialApps::PROVIDER_FACEBOOK);

        if (!$brand_social_accounts) {
            return;
        }

        foreach ($brand_social_accounts as $brand_social_account) {
            try {
                $facebook_client->setToken($brand_social_account->token);
                $pageData = $facebook_client->getPageInfo('/'.$brand_social_account->social_media_account_id, array('fields' => 'id,about,can_post,category,checkins,country_page_likes,cover,has_added_app,is_community_page,is_published,new_like_count,likes,link,location,name,offer_eligible,promotion_eligible,talking_about_count,unread_message_count,unread_notif_count,unseen_message_count,username,were_here_count'));
                $pictureUrl = $facebook_client->getPageInfo('/'.$brand_social_account->social_media_account_id, array('fields' => 'picture.width(200).height(200)'));

                $pictureUrl = $pictureUrl['picture']->data->url;

                $brand_social_account->about = $pageData['about'];
                $brand_social_account->name = $pageData['name'];
                $brand_social_account->picture_url = $pictureUrl;
                $brand_social_account->store = json_encode($pageData);

                $brand_social_account_service->updateBrandSocialAccount($brand_social_account);

            } catch (Exception $e) {
                $msg = $brand_social_account_service->getErrorMessage($brand_social_account, $e);
                $this->logError('UpdateSocialAccountInfoTask @updateFBPageInfo() brand_social_account_id = '.$brand_social_account->id . " msg=" . $msg, $e);
            }
        }
    }

    /**
     * Twitterアカウントの情報を更新します。
     */
    private function updateTWAccountInfo() {

        require_once('vendor/codebird-php/src/codebird.php');

        define('CODEBIRD_RETURNFORMAT_OBJECT', 0);
        define('CODEBIRD_RETURNFORMAT_ARRAY', 1);

        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');

        $brand_social_accounts = $brand_social_account_service->getBrandsSocialAccountsBySocialAppIdAndExpiredFlg(SocialApps::PROVIDER_TWITTER);

        $client = $this->initTWClient();

        if (!$client) return;

        foreach ($brand_social_accounts as $brand_social_account) {
            try {
                $client->setToken($brand_social_account->token, $brand_social_account->token_secret);

                $store = $client->account_verifyCredentials();

                if ($err_mess = $brand_social_account_service->getErrorMessage($brand_social_account, $store)) {
                    throw new Exception($err_mess);
                }

                $brand_social_account->name = $store['name'];
                $brand_social_account->screen_name = $store['screen_name'];
                $brand_social_account->about = $store['description'];
                $brand_social_account->picture_url = $this->getTWOriginalProfileImage($store['profile_image_url_https']);
                $brand_social_account->store = json_encode($store);

                $brand_social_account_service->updateBrandSocialAccount($brand_social_account);
            }catch (Exception $e) {
                $this->logError("UpdateSocialAccountInfoTask @updateTWAccountInfo() brand_social_account_id = " . $brand_social_account->id, $e);
            }
        }
    }

    /**
     * Youtubeチャンネルの情報を更新します。
     */
    private function updateYTChannelInfo() {

        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');

        $brand_social_accounts = $brand_social_account_service->getBrandsSocialAccountsBySocialAppIdAndExpiredFlg(SocialApps::PROVIDER_GOOGLE);

        $client = $this->initYTClient();

        foreach ($brand_social_accounts as $brand_social_account) {

            try {
                $client->setAccessToken($brand_social_account->token);
                $plus = new Google_Oauth2Service($client);
                $youtube = new Google_YouTubeService($client);
                $channelsResponse = $youtube->channels->listChannels('id', array(
                    'mine' => 'true',
                ));
                $userinfo = $plus->userinfo->get();
                $userinfo['channelId'] = $channelsResponse['items'][0]['id'];

                $brand_social_account->name = $userinfo['name'];
                $brand_social_account->screen_name = $userinfo['name'];
                $brand_social_account->picture_url = $userinfo['picture'] . '?sz=200';
                $brand_social_account->store = json_encode($userinfo);

                $brand_social_account_service->updateBrandSocialAccount($brand_social_account);

            } catch (Exception $e) {
                $this->logError("UpdateSocialAccountInfoTask @updateYTChannelInfo() brand_social_account_id = " . $brand_social_account->id, $e);
            }
        }
    }

    /**
     * Instagramアカウントの情報を更新します。
     */
    private function updateIGAccountInfo() {
        AAFW::import('jp.aainc.classes.CacheManager');
        AAFW::import('jp.aainc.vendor.instagram.Instagram');

        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');

        $instagram = new Instagram();
        $cache_manager = new CacheManager();
        $brand_social_accounts = $brand_social_account_service->getBrandsSocialAccountsBySocialAppIdAndExpiredFlg(SocialApps::PROVIDER_INSTAGRAM);

        foreach ($brand_social_accounts as $brand_social_account) {
            try {
                $response = $instagram->getAccountInfo($brand_social_account->social_media_account_id, $brand_social_account->token);

                if (!$response || $err_mess = $brand_social_account_service->getErrorMessage($brand_social_account, $response)) {
                    throw new Exception('UpdateInstagramAccount: '.$err_mess);
                }

                $brand_social_account->name = $response->data->username;
                $brand_social_account->picture_url = $response->data->profile_picture;
                $brand_social_account->store = json_encode($response->data);
                $brand_social_account_service->updateBrandSocialAccount($brand_social_account);

                $cache_manager->deletePanelCache($brand_social_account->brand_id);

            } catch (Exception $e) {
                $this->logError("UpdateSocialAccountInfoTask @updateIGAccountInfo() brand_social_account_id = " . $brand_social_account->id, $e);
            }
        }
    }

    private function initYTClient() {
        try {
            AAFW::import('jp.aainc.vendor.google.Google_Client');
            AAFW::import('jp.aainc.vendor.google.contrib.Google_PlusService');
            AAFW::import('jp.aainc.vendor.google.contrib.Google_YouTubeService');
            AAFW::import('jp.aainc.vendor.google.contrib.Google_Oauth2Service');

            $client = new Google_Client();
            $client->setClientId($this->config->query('@google.Google.ClientID'));
            $client->setClientSecret($this->config->query('@google.Google.ClientSecret'));
            $client->setRedirectUri(Util::getHttpProtocol() . '://' . Util::getMappedServerName() . '/' . $this->config->query('@google.Google.RedirectUri'));
            $scope = array();
            $apiBase = $this->config->query('@google.Google.ApiBaseUrl');
            foreach ($this->config->query('@google.Google.Scope') as $url) {
                array_push($scope, $apiBase . '/' . $url);
            }
            $client->setScopes($scope);
            return $client;
        } catch (Exception $e) {
            $this->logError("UpdateSocialAccountInfoTask @initYTClient()", $e);
            return null;
        }
    }

    private function initTWClient() {
        try {
            \Codebird\Codebird::setConsumerKey(
                $this->config->query('@twitter.Admin.ConsumerKey'),
                $this->config->query('@twitter.Admin.ConsumerSecret')
            );

            $client = \Codebird\Codebird::getInstance();
            $client->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);

            return $client;
        } catch (Exception $e) {
            $this->logError("UpdateSocialAccountInfoTask @initTWClient()", $e);
            return null;
        }
    }

	public function finish() {
        $this->logger->info("UpdateSocialAccountInfoTask @finish()");
	}

    public function getTWOriginalProfileImage($img_url) {
        if (substr_count($img_url, '_bigger.') == 1) {
            $img_url = str_replace("_bigger.", ".", $img_url);

        } elseif (substr_count($img_url, '_normal.') == 1) {
            $img_url = str_replace("_normal.", ".", $img_url);

        } elseif (substr_count($img_url, '_mini.') == 1) {
            $img_url = str_replace("_mini.", ".", $img_url);
        }
        return $img_url;
    }
}