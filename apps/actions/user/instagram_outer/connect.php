<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.exception.EntityNotFoundException');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class connect extends BrandcoGETActionBase {
    public $NeedOption = array();
    public $NeedLogin = true;
    protected $instagram;
    protected $ContainerName = 'login_outer';

    private $errorPackage = 'sns';
    private $errorAction ='login_outer';

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

    public function validate() {
        // 認証チェック
        if ($this->getSession('login_outer') !== 1) {
            return false;
        }

        $this->NeedPublic = true;
        return true;
    }

    function doAction() {
        $social_app = $this->createService('SocialAppService')->getSocialAppByProvider(SocialApps::PROVIDER_INSTAGRAM, 1, true);

        if (!$social_app) {
            return $this->instagramRedirect(Util::rewriteUrl($this->errorPackage, $this->errorAction));
        }

        $this->instagram = $this->getInstagram();

        if (!$this->code) {
            $brand = $this->getBrand();
            $baseUrl = config('Protocol.Secure') . '://' . config('Domain.brandco') . '/';
            $_SESSION['instagram_redirect_url'] = Util::rewriteUrl(
                'instagram_outer',
                'connect',
                [],
                ['token' => $this->GET['token']],
                $baseUrl
            );
            $redirect_url = $this->instagram->buildAuthUrl();
            $this->instagramRedirect($redirect_url);
        }

        $this->setSession('instagram_redirect_url', null);
        $this->instagram->authenticate($this->code);

        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->createService('BrandSocialAccountService');

        $brand_social_account = $brand_social_account_service->getBrandSocialAccount(
            $this->getBrand()->id,
            $this->instagram->getUserInfo()->id,
            $social_app->id
        );
        if (!$brand_social_account) {
            $brand_social_account = $brand_social_account_service->getHiddenBrandSocialAccountByAppId(
                $this->instagram->getUserInfo()->id,
                $social_app->id
            );
        }

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

        if ($brand_social_account) {
            $brand_social_account = $this->fillBrandSocialAccount($brand_social_account, $brand_outer_token);
            if ($brand_social_account->hidden_flg == 1) {
                $brand_social_account->order_no = $brand_social_account_service->getMaxOrder($this->getBrand()->id) + 2;
            }
            $brand_social_account->hidden_flg = 0;
            $brand_social_account_service->updateBrandSocialAccountAndStream($brand_social_account);
        } else {
            $brand_social_account = $brand_social_account_service->createEmptyBrandSocialAccount();
            $brand_social_account = $this->fillBrandSocialAccount($brand_social_account, $brand_outer_token);
            $brand_social_account->order_no = $brand_social_account_service->getMaxOrder($this->getBrand()->id) + 1;
            $brand_social_account_service->createBrandSocialAccount($brand_social_account, $this->getBrand());
        }

        $this->setSession('brand_social_account_id', null);
        $this->setSession('brand_social_account_id', $brand_social_account->id);

        return 'redirect: ' . Util::rewriteUrl(
            'instagram_outer',
            'connect_finish',
            [],
            ['token' => $this->GET['token']]
        );
    }

    function instagramRedirect($redirect_url) {
        echo "<script type='text/javascript'>top.location.href = '$redirect_url';</script>";
        exit;
    }

    private function fillBrandSocialAccount($brand_social_account, $brand_outer_token) {
        $brand_social_account->user_id = $brand_outer_token->user_id;
        $brand_social_account->social_media_account_id = $this->instagram->getUserInfo()->id;
        $brand_social_account->social_app_id = SocialApps::PROVIDER_INSTAGRAM;
        $brand_social_account->token = $this->instagram->getAccessToken();
        $brand_social_account->token_update_at = date("Y-m-d H:i:s", time());
        $brand_social_account->name = $this->instagram->getUserInfo()->username;
        $brand_social_account->screen_name = $this->instagram->getUserInfo()->full_name ? $this->instagram->getUserInfo()->full_name : $this->instagram->getUserInfo()->username;
        $brand_social_account->about = $this->instagram->getUserInfo()->bio;
        $brand_social_account->picture_url = $this->instagram->getUserInfo()->profile_picture;
        $brand_social_account->store = json_encode($this->instagram->getUserInfo());

        return $brand_social_account;
    }
}
