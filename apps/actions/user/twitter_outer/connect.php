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
        //user access denied
        if (isset($this->GET['denied'])) {
            if ($this->callback_url) {
                return 'redirect: ' . $this->callback_url;
            } else {
                return 'redirect: ' . Util::rewriteUrl(
                    'sns',
                    'login_outer_form',
                    [],
                    ['token' => $this->GET['token']]
                );
            }
        }

        $social_app = $this->createService('SocialAppService')->getSocialAppByProvider(SocialApps::PROVIDER_TWITTER, 1, true);

        if ($social_app) {
            //admin権限をセット
            $this->setMode( self::BRANDCO_MODE_ADMIN );

            /** @var BrandSocialAccountService $brand_social_account_service */
            $brand_social_account_service = $this->createService('BrandSocialAccountService');
            $twitter = $this->getTwitter()->twCheckLogin();

            $brand_social_account = $brand_social_account_service->getBrandSocialAccount($this->getBrand()->id, $twitter->getUser(), $social_app->id);
            if (!$brand_social_account) {
                $brand_social_account = $brand_social_account_service->getHiddenBrandSocialAccountByAppId($twitter->getUser(), $social_app->id);
            }

            $store = $twitter->checkCredentials();
            if(!$store) {
                return 'redirect: ' . Util::rewriteUrl(
                    'sns',
                    'login_outer_form',
                    [],
                    ['token' => $this->GET['token']]
                );
            }
            $data = json_decode($store);
            $picture_url = $this->getOriginalProfileImage($data->profile_image_url_https);
            $date = date("Y-m-d H:i:s", time());

            // user_idの取得
            $brand_outer_token_service = $this->createService('BrandOuterTokenService');
            $brand_outer_token = $brand_outer_token_service->getBrandOuterTokenByToken($this->GET['token']);
            if (!$brand_outer_token) {
                $e = new EntityNotFoundException(
                    'brand_outer_token record not found. token=' . $this->POST['token']
                );
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

            // 管理者アカウントをAppAccountsに保存しておく
            if($brand_social_account){
                $brand_social_account->user_id = $brand_outer_token->user_id;
                $brand_social_account->social_media_account_id = $twitter->getUser();
                $brand_social_account->social_app_id = SocialApps::PROVIDER_TWITTER;
                $brand_social_account->token = $twitter->token->key;
                $brand_social_account->token_secret = $twitter->token->secret;
                $brand_social_account->token_update_at = $date;
                $brand_social_account->name = json_decode($store)->name;
                $brand_social_account->screen_name = json_decode($store)->screen_name;
                $brand_social_account->about = $data->description;
                $brand_social_account->picture_url = $picture_url;
                $brand_social_account->store = $store;
                if ($brand_social_account->hidden_flg == 1) {
                    $brand_social_account->order_no = $brand_social_account_service->getMaxOrder($this->getBrand()->id) + 1;
                }
                $brand_social_account->hidden_flg = 0;
                $brand_social_account_service->updateBrandSocialAccountAndStream($brand_social_account);
            } else {
                $brand_social_account = $brand_social_account_service->createEmptyBrandSocialAccount();
                $brand_social_account->user_id = $brand_outer_token->user_id;
                $brand_social_account->social_media_account_id = $twitter->getUser();
                $brand_social_account->social_app_id = SocialApps::PROVIDER_TWITTER;
                $brand_social_account->token = $twitter->token->key;
                $brand_social_account->token_secret = $twitter->token->secret;
                $brand_social_account->token_update_at = $date;
                $brand_social_account->name = json_decode($store)->name;
                $brand_social_account->screen_name = json_decode($store)->screen_name;
                $brand_social_account->about = $data->description;
                $brand_social_account->order_no = $brand_social_account_service->getMaxOrder($this->getBrand()->id) + 1;
                $brand_social_account->picture_url = $picture_url;
                $brand_social_account->store = $store;
                $brand_social_account_service->createBrandSocialAccount($brand_social_account, $this->getBrand());
            }
        }
        $this->setSession('tw_onetime_oauth_token', null);
        $this->setSession('tw_onetime_oauth_secret', null);

        $this->setSession('brand_social_account_id', null);
        $this->setSession('brand_social_account_id', $brand_social_account->id);

        return 'redirect: ' . $this->callback_url;
    }

    public function getOriginalProfileImage($img_url) {
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
