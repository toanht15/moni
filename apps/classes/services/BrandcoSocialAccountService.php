<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class BrandcoSocialAccountService extends aafwServiceBase {

    /** @var BrandcoSocialAccounts $brandco_social_accounts */
    protected $brandco_social_accounts;

    public function __construct() {
        $this->brandco_social_accounts = $this->getModel("BrandcoSocialAccounts");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param BrandcoSocialAccount $brandco_social_account
     */
    public function saveBrandcoSocialAccount(BrandcoSocialAccount $brandco_social_account) {
        $this->brandco_social_accounts->begin();
        try {
            $this->brandco_social_accounts->save($brandco_social_account);
        } catch (Exception $e) {
            $this->logger->error("BrandcoSocialAccountService#saveBrandcoSocialAccount Error");
            $this->logger->error($e);
            $this->brandco_social_accounts->rollback();
        }
        $this->brandco_social_accounts->commit();
    }

    /**
     * @return mixed
     */
    public function createEmptyBrandcoSocialAccount() {
        return $this->brandco_social_accounts->createEmptyObject();
    }

    /**
     * @param $userId
     * @param $socialAppId
     * @return entity
     */
    public function getBrandcoSocialAccount($userId, $socialAppId) {
        $filter = array(
            "user_id" => $userId,
            "social_app_id" => $socialAppId,
        );
        return $this->brandco_social_accounts->findOne($filter);
    }

    /**
     * @param $userId
     * @return aafwEntityContainer|array
     */
    public function getBrandcoSocialAccounts($userId) {
        $filter = array(
            "user_id" => $userId
        );
        return $this->brandco_social_accounts->find($filter);
    }

    /**
     * @param $userId
     * @param $socialAppId
     */
    public function deleteBrandcoSocialAccount($userId, $socialAppId) {
        $this->brandco_social_accounts->begin();
        try {
            $brandco_social_account = $this->getBrandcoSocialAccount($userId, $socialAppId);
            if ($brandco_social_account) {
                $brandco_social_account->del_flg = 1;
                $this->brandco_social_accounts->save($brandco_social_account);
            }
        } catch (Exception $e) {
            $this->logger->error("BrandcoSocialAccountService#deleteBrandcoSocialAccount Error");
            $this->logger->error($e);
            $this->brandco_social_accounts->rollback();
        }
        $this->brandco_social_accounts->commit();
    }

    /**
     * @param $userId
     */
    public function deleteBrandcoSocialAccounts($userId) {
        $this->brandco_social_accounts->begin();
        try {
            $brandco_social_accounts = $this->getBrandcoSocialAccounts($userId);
            foreach ($brandco_social_accounts as $brandco_social_account) {
                $brandco_social_account->del_flg = 1;
                $this->brandco_social_accounts->save($brandco_social_account);
            }
        } catch (Exception $e) {
            $this->logger->error("BrandcoSocialAccountService#deleteBrandcoSocialAccounts Error");
            $this->logger->error($e);
            $this->brandco_social_accounts->rollback();
        }
        $this->brandco_social_accounts->commit();
    }

}
