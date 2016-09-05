<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.vendor.twitter.Twitter');

class connect_ads_account extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_TWITTER_ADS);
    public $NeedLogin = true;
    public $NeedAdminLogin = true;

    public function validate () {
        return true;
    }

    function doAction() {

        //user access denied
        if (isset($this->GET['denied'])) {
            if ($this->callback_url) {
                return 'redirect: ' . $this->callback_url;
            } else {
                return 'redirect: ' . Util::rewriteUrl('admin-fan', 'ads_list', array(), array('showModal'=> SocialApps::PROVIDER_TWITTER));
            }
        }

        //エラーが発生
        if (isset($this->GET['error_reason']) && $this->GET['error_reason']) {
            $this->Data['error'] = '連携が失敗しました。';
            return 'user/brandco/twitter/connect_ads_account.php';
        }

        if ($this->refreshTop) {
            $this->Data['error'] = '遷移中';
            return 'user/brandco/twitter/connect_ads_account.php';
        }

        if ($this->callback_url) {
            $callback_url = $this->callback_url;
        } else {
            $callback_url = Util::rewriteUrl('admin-fan', 'ads_list', array(), array('showModal'=> SocialApps::PROVIDER_TWITTER));
        }

        //admin権限をセット
        $this->setMode(Twitter::BRANDCO_MODE_ADS);
        $twitter_client = new Twitter($this->config->query('@twitter.Ads.ConsumerKey'), $this->config->query('@twitter.Ads.ConsumerSecret'), null, null, $this->getMode());

        if(!$_GET['oauth_token'] || !$_GET['oauth_verifier']) {
            $twitter = $twitter_client->twCheckLogin($callback_url);
        } else {
            $twitter = $twitter_client->twCheckLogin();
        }

        $store = $twitter->checkCredentials();

        if(!$store) {
            if ($this->callback_url) {
                return 'redirect: ' . $this->callback_url;
            } else {
                $this->Data['error'] = '連携が失敗しました。';
                return 'user/brandco/twitter/connect_ads_account.php';
            }
        }



        //create or update user
        $ads_user_data = array(
            'brand_user_relation_id' => $this->getBrandsUsersRelation()->id,
            'social_app_id' => SocialApps::PROVIDER_TWITTER,
        );

        $ads_user_data['access_token'] = $twitter->token->key;
        $ads_user_data['secret_access_token'] = $twitter->token->secret;
        $ads_user_data['name'] = json_decode($store)->name;
        $ads_user_data['social_account_id'] = $twitter->getUser();

        $this->Data["user_name"] = $ads_user_data['name'];

        /** @var AdsService $ads_service */
        $ads_service = $this->createService('AdsService');
        $ads_user = $ads_service->createOrUpdateAdsUser($ads_user_data);

        $this->Data["ads_user_id"] = $ads_user->id;
        $this->Data["sns_account_id"] = $ads_user->social_account_id;

        $twitter_ads_api_client = new TwitterAdsApiClient($ads_user->access_token, $ads_user->secret_access_token);

        try{

            $accounts = $twitter_ads_api_client->getAdsAccount();

            $this->Data['ads_accounts'] = array();

            foreach($accounts as $account) {
                if (!$ads_service->findAdsAccountsByAdsUserIdAndSnsAccountId($ads_user->id, $account->id, SocialApps::PROVIDER_TWITTER)) {
                    $this->Data['ads_accounts'][] = $account;
                }
            }

        } catch(Exception $e) {
            $this->Data['error'] = '連携が失敗しました。';
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('Twitter connect_ads_account#doAction() Exception');
            $logger->error($e->getMessage());
        }

        return 'user/brandco/twitter/connect_ads_account.php';
    }
}
