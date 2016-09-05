<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_ads_account extends BrandcoPOSTActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ContainerName = 'twitter';

    public function validate () {

        $validator = new AdsValidator($this->getBrandsUsersRelation()->id);

        if (!$validator->isValidAdsUserId($this->ads_user_id)) {
            return '404';
        }

        return true;
    }

    public function doAction () {

        try {

            /** @var AdsService $ads_service */
            $ads_service = $this->createService('AdsService');
            $ads_user = $ads_service->findAdsUserById($this->ads_user_id);

            $twitter_ads_api_client = new TwitterAdsApiClient($ads_user->access_token, $ads_user->secret_access_token);

            $accounts_data = $twitter_ads_api_client->fetchAdsAccountsInfo($this->account_ids, $ads_user);

            foreach ($accounts_data as $account_data) {
                $ads_service->createOrUpdateAdsAccount($account_data);
            }

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error("save_ads_account doAction error");
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            return 'redirect: ' . Util::rewriteUrl('twitter', 'connect_ads_account', array(), array('error_reason'=>1));
        }

        return 'redirect: ' . Util::rewriteUrl('twitter', 'connect_ads_account', array(), array('refreshTop'=>1, 'callback_url' => $this->callback_url));
    }
}