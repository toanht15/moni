<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class connect extends BrandcoGETActionBase {
    public $NeedOption = array();
    public $NeedLogin = true;
    public $NeedAdminLogin = true;
    protected $instagram;

    public function validate() {
        return true;
    }

    function doAction() {
        if (isset($this->error)) {
            if ($this->callback_url) {
                return 'redirect: ' . $this->callback_url;
            } else {
                return 'redirect: ' . Util::rewriteUrl('', '', array(), array('connect' => 'insta'));
            }
        }

        $social_app = $this->createService('SocialAppService')->getSocialAppByProvider(SocialApps::PROVIDER_INSTAGRAM, 1, true);

        if (!$social_app) {
            if ($this->callback_url) {
                return $this->instagramRedirect($this->callback_url);
            } else {
                return $this->instagramRedirect(Util::rewriteUrl('', '', array(), array('connect' => 'insta')));
            }
        }

        $this->instagram = $this->getInstagram();

        if (!$this->code) {
            if ($this->callback_url) {
                $_SESSION['callback_param'] = $this->callback_url;
            }
            $_SESSION['instagram_redirect_url'] = Util::rewriteUrl('instagram', 'connect');

            $redirect_url = $this->instagram->buildAuthUrl();
            $this->instagramRedirect($redirect_url);
        }

        $this->setSession('instagram_redirect_url', null);
        $this->setSession('callback_param', null);

        $brand_social_accounts = aafwEntityStoreFactory::create('BrandSocialAccounts');
        try {
            $brand_social_accounts->begin();

            $this->instagram->authenticate($this->code);

            /** @var BrandSocialAccountService $brand_social_account_service */
            $brand_social_account_service = $this->createService('BrandSocialAccountService');

            $brand_social_account = $brand_social_account_service->getBrandSocialAccount($this->getBrand()->id, $this->instagram->getUserInfo()->id, $social_app->id);

            if ($brand_social_account) {
                $brand_social_account = $this->fillBrandSocialAccount($brand_social_account);
                if ($brand_social_account->hidden_flg == 1) {
                    $brand_social_account->order_no = $brand_social_account_service->getMaxOrder($this->getBrand()->id) + 1;
                }
                $brand_social_account->hidden_flg = 0;
                $brand_social_account_service->updateBrandSocialAccountAndStream($brand_social_account);
            } else {
                $brand_social_account = $brand_social_account_service->createEmptyBrandSocialAccount();
                $brand_social_account = $this->fillBrandSocialAccount($brand_social_account);
                $brand_social_account->order_no = $brand_social_account_service->getMaxOrder($this->getBrand()->id) + 1;
                $brand_social_account_service->createBrandSocialAccount($brand_social_account, $this->getBrand());
            }

            $brand_social_accounts->commit();
        } catch (Exception $e) {
            $brand_social_accounts->rollback();

            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('InstagramConnect@doAction Error ' . $e);

            if ($this->callback_url) {
                return 'redirect: ' . $this->callback_url;
            } else {
                return 'redirect: ' . Util::rewriteUrl('', '', array(), array('connect' => 'insta', 'mid' => 'failed'));
            }
        }

        if ($this->callback_url) {
            return 'redirect: ' . $this->callback_url;
        } else {
            return 'redirect: ' . Util::rewriteUrl('', '', array(), array('connect' => 'insta'));
        }
    }

    function instagramRedirect($redirect_url) {
        echo "<script type='text/javascript'>top.location.href = '$redirect_url';</script>";
        exit;
    }

    private function fillBrandSocialAccount($brand_social_account) {
        $brand_social_account->user_id = $this->getBrandsUsersRelation()->user_id;
        $brand_social_account->social_media_account_id = $this->instagram->getUserInfo()->id;
        $brand_social_account->social_app_id = SocialApps::PROVIDER_INSTAGRAM;
        $brand_social_account->token = $this->instagram->getAccessToken();
        $brand_social_account->token_update_at = date("Y-m-d H:i:s", time());
        $brand_social_account->name = $this->instagram->getUserInfo()->username;
        $brand_social_account->screen_name = $this->instagram->getUserInfo()->full_name ? $this->instagram->getUserInfo()->full_name : $this->instagram->getUserInfo()->username;
        $brand_social_account->about = $this->instagram->getUserInfo()->bio;
        $brand_social_account->picture_url = $this->instagram->getUserInfo()->profile_picture;
        $brand_social_account->store = json_encode($this->instagram->getUserInfo());
        $brand_social_account->token_expired_flg = BrandSocialAccounts::TOKEN_NOT_EXPIRE;
        return $brand_social_account;
    }
}
