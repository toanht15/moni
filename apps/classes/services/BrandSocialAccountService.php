<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class BrandSocialAccountService extends aafwServiceBase {

    /** @var BrandSocialAccounts $brand_social_accounts */
    protected $brand_social_accounts;

	public function __construct() {
		$this->brand_social_accounts = $this->getModel("BrandSocialAccounts");
        $this->settings = aafwApplicationConfig::getInstance();
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
	}

	public function createBrandSocialAccount($brandSocialAccount, $brand) {
		$this->brand_social_accounts->begin();
		try {
            $brandSocialAccount->brand_id = $brand->id;
			$this->brand_social_accounts->save($brandSocialAccount);

		} catch (Exception $e) {
			$this->logger->error("BrandSocialAccountService#createBrandSocialAccount Error");
			$this->logger->error($e);
			$this->brand_social_accounts->rollback();
		}
		$this->brand_social_accounts->commit();

        $service_factory = new aafwServiceFactory ();
        if($brandSocialAccount->social_app_id == SocialApps::PROVIDER_FACEBOOK) {
            $facebook_stream_service = $service_factory->create('FacebookStreamService');
            $facebook_stream_service->createStreamAndCrawlerUrl($brand, $brandSocialAccount, array(
                'kind' => FacebookStreamService::KIND_USER_TIME_LINE,
                'entry_hidden_flg' => 1,
            ));
        } elseif($brandSocialAccount->social_app_id == SocialApps::PROVIDER_TWITTER){
            $twitter_stream_service = $service_factory->create('TwitterStreamService');
            $twitter_stream_service->createStreamAndCrawlerUrl($brand, $brandSocialAccount, array(
                'kind' => TwitterStreamService::KIND_USER_TIME_LINE,
                'entry_hidden_flg' => 1,
            ));
        }elseif($brandSocialAccount->social_app_id == SocialApps::PROVIDER_GOOGLE){
            $youtube_stream_service = $service_factory->create('YoutubeStreamService');
            $youtube_stream_service->createStreamAndCrawlerUrl($brand, $brandSocialAccount, array(
                'kind' => YoutubeStreamService::KIND_USER_TIME_LINE,
                'entry_hidden_flg' => 1,
            ));
        } elseif ($brandSocialAccount->social_app_id == SocialApps::PROVIDER_INSTAGRAM) {
            $instagram_stream_service = $service_factory->create('InstagramStreamService');
            $instagram_stream_service->createStreamAndCrawlerUrl($brand, $brandSocialAccount, array(
                'kind' => InstagramStreamService::KIND_RECENT_MEDIA,
                'entry_hidden_flg' => 1
            ));
        }

	}

	public function updateBrandSocialAccount($brandSocialAccount) {
		$this->brand_social_accounts->save($brandSocialAccount);
	}

    public function updateBrandSocialAccountAndStream($brandSocialAccount) {
        $this->brand_social_accounts->begin();

        try {

            $this->updateBrandSocialAccount($brandSocialAccount);

            $service_factory = new aafwServiceFactory ();
            if ($brandSocialAccount->social_app_id == SocialApps::PROVIDER_FACEBOOK) {

                /** @var FacebookStreamService $facebook_stream_service */
                $facebook_stream_service = $service_factory->create('FacebookStreamService');
                $facebook_stream = $brandSocialAccount->getFacebookStream();
                $facebook_stream_service->updateStreamAndCrawlerUrl($facebook_stream, array('entry_hidden_flg' => 1));

            } elseif ($brandSocialAccount->social_app_id == SocialApps::PROVIDER_TWITTER) {
                $twitter_stream_service = $service_factory->create('TwitterStreamService');
                $twitter_stream = $brandSocialAccount->getTwitterStream();
                $twitter_stream_service->updateStreamAndCrawlerUrl($twitter_stream, array('entry_hidden_flg' => 1));

            } elseif ($brandSocialAccount->social_app_id == SocialApps::PROVIDER_GOOGLE) {
                $youtube_stream_service = $service_factory->create('YoutubeStreamService');
                $youtube_stream = $brandSocialAccount->getYoutubeStream();
                $youtube_stream_service->updateStreamAndCrawlerUrl($youtube_stream, array('entry_hidden_flg' => 1));

            } elseif ($brandSocialAccount->social_app_id == SocialApps::PROVIDER_INSTAGRAM) {
                $instagram_stream_service = $service_factory->create('InstagramStreamService');
                $instagram_stream = $brandSocialAccount->getInstagramStream();
                $instagram_stream_service->updateStreamAndCrawlerUrl($instagram_stream, array('entry_hidden_flg' => 1));

            }

            $this->brand_social_accounts->commit();
        } catch (Exception $e) {
            $this->logger->error("SocialAccountService#updateBrandSocialAccountAndStream Error");
            $this->logger->error($e);
            $this->brand_social_accounts->rollback();
        }
    }

	public function createEmptyBrandSocialAccount() {
		return $this->brand_social_accounts->createEmptyObject();
	}

    /**
     * @param $social_media_account_id
     * @param $social_app_id
     * @return entity
     */
    public function getBrandSocialAccountByAccountId($social_media_account_id, $social_app_id) {
        $filter = array(
            'social_media_account_id' => $social_media_account_id,
            'social_app_id' => $social_app_id
        );

        return $this->brand_social_accounts->findOne($filter);
    }

	public function getBrandSocialAccount($brandId, $socialMediaAccountId, $socialAppId, $hidden_flg = null, $expired_flg = null){

        $filter = array(
            "social_media_account_id" => $socialMediaAccountId,
            "social_app_id" => $socialAppId,
            "brand_id" => $brandId
        );

        if (isset($hidden_flg)) {
            $filter["hidden_flg"] = $hidden_flg;
        }
        if (isset($expired_flg)) {
            $filter["token_expired_flg"] = $expired_flg;
        }

		return $this->brand_social_accounts->findOne($filter);
	}

    public function getBrandsSocialAccountsBySocialAppIdAndExpiredFlg($socialAppId, $token_expired_flg = 0) {
        $filter = array(
            "social_app_id" => $socialAppId,
            "hidden_flg" => "0",
            "token_expired_flg" => $token_expired_flg
        );
        return $this->brand_social_accounts->find($filter);
    }

    public function getHiddenBrandSocialAccountByAppId($socialMediaAccountId, $socialAppId) {
        $filter = array(
            "social_media_account_id" => $socialMediaAccountId,
            "social_app_id" => $socialAppId,
            "hidden_flg" => '1'
        );
        return $this->brand_social_accounts->findOne($filter);
    }

    public function getBrandSocialAccountById($id){
        $filter = array(
            "id" => $id
        );
        return $this->brand_social_accounts->findOne($filter);
    }

    public function getBrandSocialAccountByEntryId($entry_id){
        $entry = $this->getEntryById($entry_id);

        /** @var StreamService $stream_service */
        $stream_service = $this->getService('StreamService');
        $stream = $stream_service->getStreamById($entry->stream_id);

        return $this->getBrandSocialAccountById($stream->brand_social_account_id);;
    }

    public function getEntryById($id){
        return $this->getBrandSocialAccountById($id);
    }

	public function getStreamByBrandSocialAccountId($id) {
        $brandSocialAccount = $this->getBrandSocialAccountById($id);

		if( $brandSocialAccount->social_app_id == SocialApps::PROVIDER_FACEBOOK ) {
			$stream = $brandSocialAccount->getFacebookStream();
		} elseif( $brandSocialAccount->social_app_id == SocialApps::PROVIDER_TWITTER ) {
			$stream = $brandSocialAccount->getTwitterStream();
		} elseif( $brandSocialAccount->social_app_id == SocialApps::PROVIDER_GOOGLE ) {
			$stream = $brandSocialAccount->getYoutubeStream();
		} elseif ($brandSocialAccount->social_app_id == SocialApps::PROVIDER_INSTAGRAM) {
            $stream = $brandSocialAccount->getInstagramStream();
        }

		return $stream;
	}

    /**
     * @param $brandId
     * @return mixed
     */
    public function getBrandSocialAccountByBrandId($brandId){
		$filter = array(
				'brand_id' => $brandId,
                'hidden_flg' => 0
		);
		return $this->brand_social_accounts->find($filter);
	}

    /**
     * ブランドに紐づくTWソーシャルアカウントを取得
     *
     * @param $brandId
     * @return mixed
     */
    public function getTwitterSocialAccountsByBrandId($brandId){
        $filter = array(
            'brand_id' => $brandId,
            'social_app_id' => SocialApps::PROVIDER_TWITTER,
            'hidden_flg' => 0,
        );
        return $this->brand_social_accounts->find($filter);
    }

    /**
     * ブランドに紐づくFBソーシャルアカウントを取得
     *
     * @param $brandId
     * @return mixed
     */
    public function getFacebookSocialAccountsByBrandId($brandId){
        $filter = array(
            'brand_id' => $brandId,
            'social_app_id' => SocialApps::PROVIDER_FACEBOOK,
            'hidden_flg' => 0,
        );
        return $this->brand_social_accounts->find($filter);
    }

    /**
     * ブランドに紐づくソーシャルアカウントを取得
     *
     * @param $brand_id
     * @param $social_app_id
     * @return mixed
     */
    public function getSocialAccountsByBrandId($brand_id, $social_app_id) {
        $filter = array(
            'brand_id' => $brand_id,
            'social_app_id' => $social_app_id,
            'hidden_flg' => 0,
        );

        return $this->brand_social_accounts->find($filter);
    }

    /**
     * @param $user_id
     * @param $social_apps
     * @return aafwEntityContainer|array|bool
     */
    public function getBrandSocialAccountsByUserId($user_id, $social_apps){
        if (!$user_id) return false;
        $filter = array(
            'user_id' => $user_id,
            'social_app_id' => $social_apps
        );
        return $this->brand_social_accounts->find($filter);
    }

    /**
     * tokenの有効期限が指定日より過ぎているアカウント一覧取得
     * @param $brand_social_accounts
     * @param $date
     * @return array
     */
    public function getFbAccessTokenExpiryAccounts($brand_social_accounts, $date) {
        $expiry_accounts = array();
        $now_date_time = date('Y-m-d');

        foreach ($brand_social_accounts as $brand_social_account) {
            // 有効期限
            $alert_date_time = date('Y-m-d', strtotime($brand_social_account->token_update_at .'+' . $date . 'day'));
            if ($now_date_time > $alert_date_time) {
                $expiry_accounts[] = $brand_social_account;
            }
        }
        return $expiry_accounts;
    }

    /**
     * tokenの有効期限が指定日と一致しているアカウント一覧取得
     * @param $brand_social_accounts
     * @param $date
     * @return array
     */
    public function getFbAccessTokenExpiryInfo($brand_social_accounts, $date){
        $expiry_accounts = array();
        $now_date_time = date('Y/m/d');

        if ($date == -1) {
            foreach ($brand_social_accounts as $brand_social_account) {
                // 過去日時のものを取得する
                $alert_date_time = date('Y/m/d', strtotime($brand_social_account->token_update_at));

                if ($now_date_time >= $alert_date_time) {
                    $info = null;
                    $info->facebook_page = $brand_social_account->social_media_account_id;
                    $info->facebook_page_name = $brand_social_account->name;
                    $info->expired_date = $alert_date_time;
                    $info->update_url = 'https://' . Util::getMappedServerName($brand_social_account->brand_id) . '/facebook/auth_result?mode=extend&page_id=' .$brand_social_account->social_media_account_id;
                    $info->user_name = $brand_social_account->getUser()->name;
                    $expiry_accounts[] = $info;
                }
            }
        } else {
            foreach ($brand_social_accounts as $brand_social_account) {
                // 有効期限
                $alert_date_time = date('Y/m/d', strtotime($brand_social_account->token_update_at .'+' . $date . 'day'));

                if ($now_date_time == $alert_date_time) {
                    $info = null;
                    $info->facebook_page = $brand_social_account->social_media_account_id;
                    $info->facebook_page_name = $brand_social_account->name;
                    $info->expired_date = $alert_date_time;
                    $info->update_url = 'https://' . Util::getMappedServerName($brand_social_account->brand_id) . '/facebook/auth_result?mode=extend&page_id=' .$brand_social_account->social_media_account_id;
                    $info->user_name = $brand_social_account->getUser()->name;
                    $expiry_accounts[] = $info;
                }
            }
        }

        return $expiry_accounts;
    }

    /**
     * @param $token
     * @param $page_id
     * @return bool
     */
    public function isFacebookPageAdmin($token, $page_id){
        try {
            $facebook_api_client = new FacebookApiClient();
            $facebook_api_client->setToken($token);

            $result = $facebook_api_client->getAdminPageAccounts();
            if (!$result['data']) return false;

            foreach ($result['data'] as $page) {
                // ユーザがページの管理者である場合は更新する
                if ($page->id == $page_id) {
                    return true;
                }
            }
        } catch (Exception $e){
            
        }
        return false;
    }

    /**
     * @param $stream
     * @param $value
     */
    public function updateHiddenFlgBrandSocialAccountByStream($stream, $value) {
        $brand_social_account = $stream->getBrandSocialAccount();
        $brand_social_account->hidden_flg = $value;
        $this->brand_social_accounts->save($brand_social_account);
    }

    /**
     * @param $brand_social_account_id
     * @param int $display_panel_limit
     */
    public function updateDisplayPanelLimit($brand_social_account_id, $display_panel_limit=0) {
        $brand_social_account = $this->getBrandSocialAccountById($brand_social_account_id);
        $brand_social_account->display_panel_limit = $display_panel_limit;
        $this->brand_social_accounts->save($brand_social_account);
    }

    /**
     * @param $brand_social_id
     * @param $order
     */
    public function updateOrder($brand_social_id, $order) {
        $brand_social_account = $this->getBrandSocialAccountById($brand_social_id);
        $brand_social_account->order_no = $order;
        $this->brand_social_accounts->save($brand_social_account);
    }

    /**
     * @param $brand_id
     * @return 最大値
     */
    public function getMaxOrder($brand_id) {
        $filter = array(
            'conditions'=> array(
                "brand_id" => $brand_id,
                "hidden_flg" => 0
            )
        );
        $service_factory = new aafwServiceFactory();

        /** @var RssStreamService $rss_stream_service */
        $rss_stream_service = $service_factory->create('RssStreamService');
        $rss_order_max = $rss_stream_service->getMaxOrder($brand_id);
        $brand_social_order_max = $this->brand_social_accounts->getMax('order_no', $filter);
        return ($rss_order_max > $brand_social_order_max) ? $rss_order_max : $brand_social_order_max;
    }

    public function getDisconnectedSNSOverMonth() {
        $date = new DateTime();
        $date->sub(new DateInterval('P1M'));
        $filter = array(
            'conditions' => array(
                'hidden_flg' => 1,
                'updated_at:<' => $date->format('Y-m-d H:i:s')
            )
        );
        return $this->brand_social_accounts->find($filter);
    }

    public function getBrandSocialAccountStore() {
        return $this->brand_social_accounts;
    }

    /**
     * responseでエラーがあったらエラーメッセージを返ります。
     *
     * @param BrandSocialAccount $brandSocialAccount
     * @param $response
     * @return null
     */
    public function getErrorMessage($brandSocialAccount, $response) {

        if (!$brandSocialAccount || !$response) {
            return null;
        }

        /** @var ManagerMailService $manager_mail_service */
        $manager_mail_service = $this->getService('ManagerMailService');

        switch ($brandSocialAccount->social_app_id) {
            case SocialApps::PROVIDER_FACEBOOK:
                //FBの場合はリクエストエラーが発生したらexceptionを生成されます。
                if (is_a($response, "Facebook\FacebookSDKException") && $response->getCode() != 200) {
                    if ($response->getMessage() == "Session has expired, or is not valid for this app.") {
                        $brandSocialAccount->token_expired_flg = BrandSocialAccounts::TOKEN_EXPIRED;
                        $this->updateBrandSocialAccount($brandSocialAccount);
                        //担当者に通知メールを送信する
                        $manager_mail_service->sendExpiredTokenNotificationMail($brandSocialAccount->id);
                    }
                    return $response->getMessage();
                }
                break;
            case SocialApps::PROVIDER_TWITTER:
                if ($response['httpstatus'] != 200) {
                    if ($response['errors'][0]['message'] == "Invalid or expired token.") {
                        $brandSocialAccount->token_expired_flg = BrandSocialAccounts::TOKEN_EXPIRED;
                        $this->updateBrandSocialAccount($brandSocialAccount);
                        //担当者に通知メールを送信する
                        $manager_mail_service->sendExpiredTokenNotificationMail($brandSocialAccount->id);
                    }
                    return $response['errors'][0]['message'];
                }
                break;
            case SocialApps::PROVIDER_GOOGLE:
                if ((is_a($response, "Google_ServiceException") && $response->getErrors()[0]["reason"] == "authError") || is_a($response, "Google_AuthException")) {
                    $brandSocialAccount->token_expired_flg = BrandSocialAccounts::TOKEN_EXPIRED;
                    $this->updateBrandSocialAccount($brandSocialAccount);
                    //担当者に通知メールを送信する
                    $manager_mail_service->sendExpiredTokenNotificationMail($brandSocialAccount->id);
                }
                return $response->getMessage();
                break;
            case SocialApps::PROVIDER_INSTAGRAM:
                if ($response->meta->code != 200) {
                    if ($response->meta->error_type == "OAuthAccessTokenException") {
                        $brandSocialAccount->token_expired_flg = BrandSocialAccounts::TOKEN_EXPIRED;
                        $this->updateBrandSocialAccount($brandSocialAccount);
                        //担当者に通知メールを送信する
                        $manager_mail_service->sendExpiredTokenNotificationMail($brandSocialAccount->id);
                    }
                }
                return $response->meta->error_message;
                break;
            default:
                break;
        }
        return null;
    }

    public function getSnsAccounts($socialAppId) {
        $filter = array(
            'social_app_id' => $socialAppId
        );
        return $this->brand_social_accounts->find($filter);
    }

}

