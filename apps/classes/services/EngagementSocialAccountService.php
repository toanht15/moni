<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class EngagementSocialAccountService extends aafwServiceBase {

    /** @var EngagementSocialAccountService $engagement_social_account_service */
    protected $engagement_social_account_service;

	public function __construct() {
		$this->engagement_social_account_service = $this->getModel('EngagementSocialAccounts');
        $this->settings = aafwApplicationConfig::getInstance();
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
	}

    /**
     * @param $cp_engagement_action_id
     * @param $brand_social_account_id
     */
    public function create($cp_engagement_action_id, $brand_social_account_id){
            $engagement_social_account = $this->engagement_social_account_service->createEmptyObject();
            $engagement_social_account->cp_engagement_action_id = $cp_engagement_action_id;
            $engagement_social_account->brand_social_account_id = $brand_social_account_id;
            $this->engagement_social_account_service->save($engagement_social_account);
    }

    /**
     * @param $cp_engagement_action_id
     */
    public function deleteByCpEngagementActionId($cp_engagement_action_id) {
        $engagement_social_account = $this->getEngagementSocialAccount($cp_engagement_action_id);
        if ($engagement_social_account->id) {
            $this->engagement_social_account_service->deletePhysical($engagement_social_account);
        }
    }

    /**
     * @param $cp_engagement_action_id
     * @return mixed
     */
    public function getEngagementSocialAccount($cp_engagement_action_id) {
        $filter = array(
            'cp_engagement_action_id' => $cp_engagement_action_id
        );
        return $this->engagement_social_account_service->findOne($filter);
    }

    /**
     * @param $brand_social_account_id
     * @return bool
     */
    public function isUsedBrandSocialAccount($brand_social_account_id) {
        if ($this->engagement_social_account_service->findOne(array('brand_social_account_id'=>$brand_social_account_id))) {
            return true;
        }
        return false;
    }
}
