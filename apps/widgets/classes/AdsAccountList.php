<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class AdsAccountList extends aafwWidgetBase {

    public function doService($params = array()) {

        /** @var AdsService $ads_service */
        $ads_service = $this->getService('AdsService');

        //Update Ads Account Info
        $ads_service->updateAdsAccountInfo($params['ads_users']);

        $params['ads_accounts'] = array();

        foreach($params['ads_users'] as $ads_user) {
            $params['ads_accounts'] = array_merge($params['ads_accounts'], $this->fetchAdsAccountInfos($ads_user));
        }

        return $params;
    }

    /**
     * @param $ads_user
     * @return array
     */
    private function fetchAdsAccountInfos($ads_user) {

        /** @var AdsService $ads_service */
        $ads_service = $this->getService('AdsService');

        $ads_accounts = $ads_service->findAdsAccountsByAdsUserId($ads_user->id);

        $ads_account_infos = array();

        foreach($ads_accounts as $ads_account) {

            $account_info = $ads_service->convertAdsAccountInfo($ads_account);

            $ads_audience = $ads_service->findAudiencesAccountsRelationsByAccountId($ads_account->id);

            $account_info['audience_count'] = $ads_audience ? $ads_audience->total() : 0;

            $list_relation_id = $this->fetchListAudienceAccountRelationId($ads_account);

            $last_target = $ads_service->findLastSendTarget($list_relation_id);

            $account_info['last_send_target'] = $last_target ? date('Y/m/d', strtotime($last_target->created_at)) : '-';

            $ads_account_infos[] = $account_info;
        }

        return $ads_account_infos;
    }

    public function isDisableAccount($account_info) {

        if($account_info['social_app_id'] == SocialApps::PROVIDER_FACEBOOK) {

            if(!$account_info['custom_audience_tos'] || !$account_info['web_custom_audience_tos']) {
                return true;
            }

        }

        return false;
    }

    /**
     * @param $ads_account
     * @return array
     */
    private function fetchListAudienceAccountRelationId($ads_account) {

        /** @var AdsService $ads_service */
        $ads_service = $this->getService('AdsService');

        $list_target_id = array();

        $audiences_accounts_relations = $ads_service->findAudiencesAccountsRelationsByAccountId($ads_account->id);

        foreach($audiences_accounts_relations as $relation) {
            $list_target_id[] = $relation->id;
        }

        return $list_target_id;
    }
}
