<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.FacebookApiClient');
AAFW::import('jp.aainc.classes.exception.EntityNotFoundException');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

use Facebook\FacebookSession as FacebookSession;
use Facebook\FacebookRequest as FacebookRequest;

class connect_app extends BrandcoPOSTActionBase {
    public $NeedOption = array();
    public $NeedLogin = true;
    public $CsrfProtect = true;

    protected $ContainerName = 'login_outer';
    protected $_ModelDefinitions = array(
        'SocialApps'
    );
    protected $Form = array();

    protected $ValidatorDefinition = array(
        'pageId' => array(
            'required' => 1,
        ),
    );

    public function doThisFirst() {
        // TODO: tokenは、sessionにて持ち回る？
        $this->Form = array(
            'package' => 'facebook_outer',
            'action' => 'connect?token=' . $this->POST['token'],
        );

        if ($this->callback_url) {
            $this->Form['action'] .= '&callback_url=' . urlencode($this->callback_url);
        }
        // Brandのdirectory_nameを取得
        $brand_service = $this->createService('BrandService');
        $brand = $brand_service->getBrandByOuterToken($this->POST['token']);
        if (!$brand) {
            return 403;
        }
        $this->GET['directory_name'] = $brand->directory_name;
        BrandInfoContainer::getInstance()->initialize($brand);
    }

    public function validate() {
        // 認証チェック
        if ($this->getSession('login_outer') !== 1) {
            return false;
        }
        $this->NeedPublic = true;

        return $this->Validator->isValid();
    }

    public function doAction() {
        $facebook_client = $this->getFacebook();

        try {
            $brand_social_account_array= [];

            foreach ($this->pageId as $pageId) {
                $params = "/$pageId";
                $facebook_client->setToken($this->POST["token_" . $pageId]);
                $pageData = $facebook_client->getPageInfo(
                    $params,
                    array('fields' => 'id,about,can_post,category,checkins,country_page_likes,cover,has_added_app,is_community_page,is_published,new_like_count,likes,link,location,name,offer_eligible,promotion_eligible,talking_about_count,unread_message_count,unread_notif_count,unseen_message_count,username,were_here_count')
                );
                $pictureUrl = $facebook_client->getPageInfo(
                    $params,
                    array('fields' => 'picture.width(200).height(200)')
                );
                $pictureUrl = $pictureUrl['picture']->data->url;

                /** @var BrandSocialAccountService $service */
                $service = $this->createService('BrandSocialAccountService');
                // 更新が必要場合
                $brand_social_account = $service->getBrandSocialAccount(
                    $this->getBrand()->id,
                    $pageId,
                    SocialApps::PROVIDER_FACEBOOK,
                    0,
                    BrandSocialAccounts::TOKEN_EXPIRED
                );
                // 隠れているページの場合
                if (!$brand_social_account) {
                    $brand_social_account = $service->getHiddenBrandSocialAccountByAppId(
                        $pageId,
                        SocialApps::PROVIDER_FACEBOOK
                    );
                }

                $token = $facebook_client->getLongAccessToken();
                $date = date("Y-m-d H:i:s", time());

                // user_idの取得
                $brand_outer_token_service = $this->createService('BrandOuterTokenService');
                $brand_outer_token = $brand_outer_token_service->getBrandOuterTokenByToken($this->POST['token']);
                if (!$brand_outer_token) {
                    throw new EntityNotFoundException(
                        'brand_outer_token record not found. token=' . $this->POST['token']
                    );
                }

                if (empty($brand_social_account)) {
                    //新しいbrand_social_account作成
                    $brand_social_account = $service->createEmptyBrandSocialAccount();
                    $brand_social_account->user_id = $brand_outer_token->user_id;
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
                    $service->createBrandSocialAccount($brand_social_account, $this->getBrand());
                } else {
                    // 既に存在している場合は、storeのみ更新する。
                    $brand_social_account->token = $token['access_token'];
                    $brand_social_account->user_id = $brand_outer_token->user_id;
                    $brand_social_account->token_update_at = $date;
                    $brand_social_account->name = $pageData['name'];
                    $brand_social_account->about = $pageData['about'];
                    $brand_social_account->picture_url = $pictureUrl;
                    if ($brand_social_account->hidden_flg == 1) {
                        $brand_social_account->order_no = $service->getMaxOrder($this->getBrand()->id) + 1;
                    }
                    $brand_social_account->need_update = 0;
                    $brand_social_account->hidden_flg = 0;
                    $brand_social_account->token_expired_flg = 0;
                    $brand_social_account->store = json_encode($pageData);
                    $service->updateBrandSocialAccountAndStream($brand_social_account);
                }
                // 完了画面に引き継ぐために、セッションに情報保存
                $brand_social_account_array[] = $brand_social_account->id;
            }
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
            if ($this->callback_url) {
                return 'redirect: ' . urldecode($this->callback_url);
            } else {
                return 'redirect: ' . Util::rewriteUrl(
                    'sns', 
                    'login_outer_form',
                    [],
                    ['token' => $this->POST['token']]
                );
            }
        }

        $this->setSession('brand_social_account_ids', $brand_social_account_array);

        return 'redirect: ' . Util::rewriteUrl(
            'facebook_outer',
            'connect_finish',
            [],
            ['token' => $this->POST['token']]
        );
    }
}
