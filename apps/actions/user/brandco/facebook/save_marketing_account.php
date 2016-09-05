<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_marketing_account extends BrandcoPOSTActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ContainerName = 'facebook';

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

            $client = new FacebookMarketingApiClient($ads_user->access_token);

            $accounts_data = $client->fetchMarketingAccountsInfo($this->account_ids, $ads_user);

            foreach ($accounts_data as $account_data) {

                if ($account_data["error"]) {
                    throw new Exception($account_data["error"]["message"]);
                }

                $ads_service->createOrUpdateAdsAccount($account_data);
            }

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error("save_marketing_account doAction error");
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            return 'redirect: ' . Util::rewriteUrl('facebook', 'connect_marketing_account', array(), array('error_reason'=>1));
        }
        
        return 'redirect: ' . Util::rewriteUrl('facebook', 'connect_marketing_account', array(), array('refreshTop'=>1, 'callback_url' => $this->callback_url));
    }
}