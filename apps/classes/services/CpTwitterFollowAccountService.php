<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpTwitterFollowAccountService extends aafwServiceBase {

    /** @var CpTwitterFollowAccountService $cp_tw_follow_account_service */
    protected $cp_tw_follow_account_service;

    public function __construct() {
        $this->cp_tw_follow_account_service = $this->getModel('CpTwitterFollowAccounts');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_twitter_follow_action_id
     * @param $brand_social_account_id
     */
    public function create($cp_twitter_follow_action_id, $brand_social_account_id){
            $account = $this->cp_tw_follow_account_service->createEmptyObject();
            $account->action_id = $cp_twitter_follow_action_id;
            $account->brand_social_account_id = $brand_social_account_id;
            $this->cp_tw_follow_account_service->save($account);
    }

    /**
     * @param $cp_twitter_follow_action_id
     */
    public function deleteByCpTwitterFollowActionId($cp_twitter_follow_action_id) {
        $account = $this->getFollowTargetSocialAccount($cp_twitter_follow_action_id);
        if ($account->id) {
            $this->cp_tw_follow_account_service->deletePhysical($account);
        }
    }

    /**
     * @param $cp_twitter_follow_action_id
     * @return mixed
     */
    public function getFollowTargetSocialAccount($cp_twitter_follow_action_id) {
        $filter = array(
            'action_id' => $cp_twitter_follow_action_id
        );
        return $this->cp_tw_follow_account_service->findOne($filter);
    }

    /**
     * @param $brand_social_account_id
     * @return bool
     */
    public function isUsedBrandSocialAccount($brand_social_account_id) {
        if ($this->cp_tw_follow_account_service->findOne(array('brand_social_account_id'=>$brand_social_account_id))) {
            return true;
        }
        return false;
    }
}
