<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.FacebookApiClient');

use Facebook\FacebookSession as FacebookSession;
use Facebook\FacebookRequest as FacebookRequest;

class connect_app extends BrandcoPOSTActionBase {

    public $NeedOption = array();
	public $NeedLogin = true;
//	public $CsrfProtect = true;

	protected $ContainerName = 'facebook';
	protected $_ModelDefinitions = array(
		'SocialApps'
	);

	protected $Form = array(
		'package' => 'facebook',
		'action' => 'connect',
	);

	protected $ValidatorDefinition = array(
		'pageId' => array(
			'required' => 1,
		),
	);

    public function doThisFirst() {

        if ($this->callback_url) {
            $this->Form['action'] .= '?callback_url=' . $this->callback_url;
        }
    }

	public function validate() {

		return $this->Validator->isValid();
	}

	public function doAction() {

		$facebook_client = $this->getFacebook();
        /** @var BrandSocialAccountService $service */
        $service = $this->createService('BrandSocialAccountService');

			foreach ($this->pageId as $pageId) {
				$params = "/$pageId";
                try {
                    $facebook_client->setToken($this->POST["token_" . $pageId]);
                    $pageData = $facebook_client->getPageInfo($params, array('fields' => 'id,about,can_post,category,checkins,country_page_likes,cover,has_added_app,is_community_page,is_published,new_like_count,likes,link,location,name,offer_eligible,promotion_eligible,talking_about_count,unread_message_count,unread_notif_count,unseen_message_count,username,were_here_count'));
                    $pictureUrl = $facebook_client->getPageInfo($params, array('fields' => 'picture.width(200).height(200)'));

                    $pictureUrl = $pictureUrl['picture']->data->url;

                    //更新が必要場合
                    $brand_social_account = $service->getBrandSocialAccount($this->getBrand()->id, $pageId, SocialApps::PROVIDER_FACEBOOK,0, BrandSocialAccounts::TOKEN_EXPIRED);
                    //隠れているページの場合
                    if (!$brand_social_account) {
                        $brand_social_account = $service->getHiddenBrandSocialAccountByAppId($pageId, SocialApps::PROVIDER_FACEBOOK);
                    }

                    $token = $facebook_client->getLongAccessToken();
                    $date = date("Y-m-d H:i:s", time());

                    if (empty($brand_social_account)) {
                        //新しいbrand_social_account作成
                        $brand_social_account = $service->createEmptyBrandSocialAccount();
                        $brand_social_account->user_id = $this->getBrandsUsersRelation()->user_id;
                        $brand_social_account->social_media_account_id = $pageId;
                        $brand_social_account->social_app_id = SocialApps::PROVIDER_FACEBOOK;
                        $brand_social_account->about = $pageData['about'];
                        $brand_social_account->token = $token['access_token'];
                        $brand_social_account->token_secret = '';
                        $brand_social_account->order_no = $service->getMaxOrder($this->getBrand()->id) + 1;
                        $brand_social_account->token_update_at = $date;
                        $brand_social_account->name = $pageData['name'];
                        $brand_social_account->picture_url = $pictureUrl;
                        $brand_social_account->store = json_encode($pageData);
                        $brand_social_account->token_expired_flg = BrandSocialAccounts::TOKEN_NOT_EXPIRE;
                        $service->createBrandSocialAccount($brand_social_account, $this->getBrand());

                    } else {
                        // 既に存在している場合は、storeのみ更新する。
                        $brand_social_account->token = $token['access_token'];
                        $brand_social_account->user_id = $this->getBrandsUsersRelation()->user_id;
                        $brand_social_account->token_update_at = $date;
                        $brand_social_account->token_expired_flg = 0;
                        $brand_social_account->name = $pageData['name'];
                        $brand_social_account->about = $pageData['about'];
                        $brand_social_account->picture_url = $pictureUrl;
                        if ($brand_social_account->hidden_flg == 1) {
                            $brand_social_account->order_no = $service->getMaxOrder($this->getBrand()->id) + 1;
                        }
                        $brand_social_account->hidden_flg = 0;
                        $brand_social_account->token_expired_flg = BrandSocialAccounts::TOKEN_NOT_EXPIRE;
                        $brand_social_account->store = json_encode($pageData);
                        $service->updateBrandSocialAccountAndStream($brand_social_account);
                    }

                } catch (Exception $e) {
                    $logger = aafwLog4phpLogger::getDefaultLogger();
                    $logger->error($e);

                    $err_mess = $service->getErrorMessage($brand_social_account, $e);
                    if ($err_mess == "Session has expired, or is not valid for this app.") {
                        $facebook_client->fbRedirectLogin();
                    }

                    if ($this->callback_url) {
                        return 'redirect: ' . urldecode($this->callback_url);
                    } else {
                        return 'redirect: ' . Util::rewriteUrl('', '', array(), array('connect'=>'fb'));
                    }
                }
			}

        if ($this->callback_url) {
            return 'redirect: ' . Util::rewriteUrl('admin-top', 'select_panel_kind', array(), array('close'=>'1', 'refreshTop'=>'1'));
        } else {
            return 'redirect: ' . Util::rewriteUrl('admin-top', 'select_panel_kind');
        }
	}

}