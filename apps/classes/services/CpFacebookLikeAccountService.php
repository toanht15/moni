<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpFacebookLikeAccountService extends aafwServiceBase {

    /** @var CpFacebookLikeAccountService $cp_fb_like_service */
    protected $cp_fb_like_service;

    public function __construct() {
        $this->cp_fb_like_service = $this->getModel('CpFacebookLikeAccounts');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_fb_like_action_id
     * @param $brand_social_account_id
     */
    public function create($cp_fb_like_action_id, $brand_social_account_id){
            $account = $this->cp_fb_like_service->createEmptyObject();
            $account->action_id = $cp_fb_like_action_id;
            $account->brand_social_account_id = $brand_social_account_id;
            $this->cp_fb_like_service->save($account);
    }

    /**
     * @param $cp_fb_like_action_id
     */
    public function deleteByCpFacebookLikeActionId($cp_fb_like_action_id) {
        $account = $this->getLikeTargetSocialAccount($cp_fb_like_action_id);
        if ($account->id) {
            $this->cp_fb_like_service->deletePhysical($account);
        }
    }

    /**
     * @param $cp_fb_like_action_id
     * @return mixed
     */
    public function getLikeTargetSocialAccount($cp_fb_like_action_id) {
        $filter = array(
            'action_id' => $cp_fb_like_action_id
        );
        return $this->cp_fb_like_service->findOne($filter);
    }

    /**
     * @param $brand_social_account_id
     * @return bool
     */
    public function isUsedBrandSocialAccount($brand_social_account_id) {
        if ($this->cp_fb_like_service->findOne(array('brand_social_account_id'=>$brand_social_account_id))) {
            return true;
        }
        return false;
    }
}
