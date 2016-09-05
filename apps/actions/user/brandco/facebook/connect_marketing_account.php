<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.FacebookApiClient');

class connect_marketing_account extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS);
    public $NeedLogin = true;
    public $NeedAdminLogin = true;

    public function validate () {
        return true;
    }

    function doAction() {

        //エラーが発生
        if (isset($this->GET['error_reason']) && $this->GET['error_reason']) {
            $this->Data['error'] = '連携が失敗しました。';
            return 'user/brandco/facebook/connect_marketing_account.php';
        }

        if ($this->refreshTop) {
            $this->Data['error'] = '遷移中';
            return 'user/brandco/facebook/connect_marketing_account.php';
        }

        try {

            if ($this->callback_url) {
                $callback_url = $this->callback_url;
            } else {
                $callback_url = Util::rewriteUrlWithoutDomainMapping('admin-fan', 'ads_list', array(), array('showModal'=> SocialApps::PROVIDER_FACEBOOK));
            }

            //admin権限をセット
            $this->setMode(FacebookApiClient::BRANDCO_MODE_MARKETING_ADMIN);

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

                //create or update user
                $ads_user_data = array(
                    'brand_user_relation_id' => $this->getBrandsUsersRelation()->id,
                    'social_app_id' => SocialApps::PROVIDER_FACEBOOK,
                );

                $facebook_client->setSession($session);
                $user_info = $facebook_client->getResponse("GET", "/me");

                $ads_user_data['access_token'] = $facebook_client->getLongAccessToken($session->getToken())["access_token"];
                $ads_user_data['name'] = $user_info['name'];
                $ads_user_data['social_account_id'] = $user_info['id'];

                /** @var AdsService $ads_service */
                $ads_service = $this->createService('AdsService');
                $ads_user = $ads_service->createOrUpdateAdsUser($ads_user_data);

                if (!$ads_user) {
                    $this->Data['error'] = '連携が失敗しました。';
                    return 'user/brandco/facebook/connect_marketing_account.php';
                }

                $this->Data["user_name"] = $user_info["name"];

                $client = new FacebookMarketingApiClient($session->getToken());

                $this->Data["ads_user_id"] = $ads_user->id;
                $this->Data["sns_account_id"] = $ads_user->social_account_id;

                try {

                    $this->Data['ads_accounts'] = array();
                    $after = '';

                    while(true) {
                        $accounts = $client->getMarketingAccounts($after);
                        if($accounts->getAfter()) {
                            $after = $accounts->getAfter();
                            foreach ($accounts as $account) {
                                if (!$ads_service->findAdsAccountsByAdsUserIdAndSnsAccountId($ads_user->id, $account->id, SocialApps::PROVIDER_FACEBOOK)) {
                                    $this->Data['ads_accounts'][] = $account;
                                }
                            }
                        } else {
                            break;
                        }
                    }

                } catch ( Exception $ex) {
                    //TODO account limit
                    $logger = aafwLog4phpLogger::getDefaultLogger();
                    $logger->error('FB connect#doAction() Exception');
                    $logger->error($ex->getMessage());
                }
            } else {
                $facebook_client->fbRedirectLogin();
            }
        } catch( Exception $ex ) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('FB connect#doAction() Exception');
            $logger->error($ex->getMessage());
        }

        return 'user/brandco/facebook/connect_marketing_account.php';
    }
}