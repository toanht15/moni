<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.FacebookApiClient');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

use Facebook\FacebookSession as FacebookSession;
use Facebook\FacebookRedirectLoginHelper as FacebookRedirectLoginHelper;
use Facebook\FacebookRequest as FacebookRequest;
use Facebook\FacebookAuthorizationException as FacebookAuthorizationException;
use Facebook\FacebookThrottleException as FacebookThrottleException;
use Facebook\FacebookServerException as FacebookServerException;
use Facebook\FacebookClientException as FacebookClientException;
use Facebook\FacebookPermissionException as FacebookPermissionException;
use Facebook\FacebookOtherException as FacebookOtherException;

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
        if (isset($this->GET['error_reason']) && $this->GET['error_reason'] == 'user_denied') {
            $this->Data['error'] = '連携が失敗しました。';
            return 'user/facebook_outer/connect.php';
        }

        $this->Data['token'] = $this->GET['token'];

        $social_app = $this->createService('SocialAppService')->getSocialAppByProvider(SocialApps::PROVIDER_FACEBOOK, 1, true);
        /** @var BrandSocialAccountService $service */
        $service = $this->createService('BrandSocialAccountService');
        $brand = $this->getBrand();

        if ($social_app) {
            try {
                if ($this->callback_url) {
                    $callback_url = $this->callback_url;
                } else {
                    $callback_url = Util::rewriteUrl('facebook_outer', 'connect', 
                        array(),
                        array('token' => $this->GET['token'])
                    );
                }
                $this->Data['callback_url'] = $callback_url;

                //admin権限をセット
                $this->setMode(self::BRANDCO_MODE_ADMIN);

                $facebook_client = new FacebookApiClient($this->getMode());
                $facebook_client->setRedirectLoginHelper($callback_url);

                if (!$this->GET['code']) {
                    $facebook_client->fbRedirectLogin();
                }

                $session = $facebook_client->getSessionFromRedirect();
                if ($session) {
                    // パーミッションをチェックする、合わない場合は再ログインしなければ生りません。
                    $facebook_client->setSession($session);
                    if (!$facebook_client->checkPermissions()) {
                        $facebook_client->fbRedirectLogin(array('auth_type' => 'rerequest'));
                    }
                    // ページリストを習得する。
                    $listPage = $facebook_client->getAdminPageAccounts();
                    $this->Data['listPage'] = $listPage['data'];
                } else {
                    $facebook_client->fbRedirectLogin();
                }
            } catch( Exception $ex ) {
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->error('FB connect_outer#doAction() Exception');
                $logger->error($ex->getMessage());
                $this->Data['error'] = '連携しているときにエラーが発生しました。再連携してください。';
                return 'user/facebook_outer/connect.php';
            }
        }

        foreach ($this->Data['listPage'] as &$page) {
            $page->token_expired = false;
            if ($service->getBrandSocialAccount(
                $brand->id,
                $page->id,
                SocialApps::PROVIDER_FACEBOOK,
                0,
                BrandSocialAccounts::TOKEN_EXPIRED
            )) {
                $page->token_expired = true;
            }
        }
        //すでに連携済みのアカウントは連携しない
        $this->Data['listPage'] = array_filter($this->Data['listPage'], function ($page) use ($service, $brand) {
            return !$service->getBrandSocialAccount(
                $brand->id,
                $page->id,
                SocialApps::PROVIDER_FACEBOOK,
                0,
                BrandSocialAccounts::TOKEN_NOT_EXPIRE
            );
        });

        $this->Data['userInfo'] = (object)$this->getSession('pl_monipla_userInfo');

        return 'user/facebook_outer/connect.php';
    }
}
