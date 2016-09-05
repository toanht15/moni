<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
AAFW::import('jp.aainc.classes.core.UserManager');

class brands_users_relation extends aafwPOSTActionBase {

    public $Secure = false;

    protected $ContainerName = 'brands_users_relation';
    protected $AllowContent = array('JSON');

    protected $brand;
    protected $brandco_user;
    protected $brandco_user_application;

    private $logger;

    public function validate() {
        return true;
    }

    function doAction() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();

        if (!$this->enterprise_id || !$this->monipla_user_id || !$this->token) {
            $json_data = $this->createAjaxResponse('ng', array(), array('Invalid params'));
            $this->assign('json_data', $json_data);
            $this->logger->error('Invalid params');
            return 'dummy.php';
        }

        // ブランド情報取得
        $this->brand = $this->getBrandInfo();
        if (!$this->brand) {
            $json_data = $this->createAjaxResponse('ng', array(), array('Get brand error'));
            $this->assign('json_data', $json_data);
            $this->logger->error('Get brand error');
            return 'dummy.php';
        }

        $brands_store = aafwEntityStoreFactory::create('Brands');

        try {
            // プラットフォームユーザ情報取得
            $platform_user = $this->getPlatformUserInfo();
            if ($platform_user->result->status != Thrift_APIStatus::SUCCESS) {
                $json_data = $this->createAjaxResponse('ng', array(), array('Thrift result error'));
                $this->assign('json_data', $json_data);
                $this->logger->error('Thrift result error');
                return 'dummy.php';
            }

            $brands_store->begin();

            //  ロック
            /** @var BrandTransactionService $transaction_service */
            $transaction_service = $this->createService('BrandTransactionService');
            $transaction_service->getBrandTransactionByIdForUpdate($this->brand->id);

            // BRANDCoユーザ作成
            $this->createBrandcoUser($platform_user);

            // ブランドユーザ作成
            $this->createBrandUser();

            $brands_store->commit();

            // 成功レスポンス
            $json_data = $this->createAjaxResponse('ok', array(), array());
            $this->assign('json_data', $json_data);
            return 'dummy.php';

        } catch (Exception $e) {
            $brands_store->rollback();
            $this->logger->error('brands_users_relation post error.'. $e);
            $json_data = $this->createAjaxResponse('ok', array(), array('create error.', $e));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }
    }

    /**
     * プラットフォーム用アクセストークン取得
     * @param $platform_user
     * @return bool|mixed
     */
    private function getPlatformAccessToken($platform_user) {
        $user_manager = new UserManager($platform_user);
        $result = $user_manager->createAuthorizationCode();

        if( $result->status == Thrift_APIStatus::FAIL) {
            $this->logger->error('brands_users_relation post#createAuthorizationCode error.');
            return false;
        }

        $result = $user_manager->getMpAccessToken($result->code);
        if( $result->status == Thrift_APIStatus::FAIL) {
            $this->logger->error('brands_users_relation post#getMpAccessToken error.');
            return false;
        }
        return $result;
    }

    /**
     * ブランド情報取得
     * @return mixed
     */
    private function getBrandInfo() {
        /** @var BrandService $brand_service */
        $brand_service = $this->createService('BrandService');
        return $brand_service->getBrandByEnterpriseIdAndToken($this->enterprise_id, $this->token);
    }

    /**
     * プラットフォームユーザ情報取得
     * @return mixed
     */
    private function getPlatformUserInfo() {
        return \Monipla\Core\MoniplaCore::getInstance()->getUserByQuery(array(
            'class' => 'Thrift_UserQuery',
            'fields' => array(
                'socialMediaType' => 'Platform',
                'socialMediaAccountID' => $this->monipla_user_id,
            )));
    }

    /**
     * ブランコユーザ作成
     * @param $platform_user
     */
    private function createBrandcoUser($platform_user) {
        /** @var UserService $user_service */
        $user_service = $this->createService('UserService');
        $this->brandco_user = $user_service->getUserByMoniplaUserId($this->monipla_user_id);

        if (!$this->brandco_user) {
            $this->brandco_user = $user_service->createEmptyUser();
            $this->brandco_user->monipla_user_id = $platform_user->id;
        }
        $this->brandco_user->name = $platform_user->name;
        $this->brandco_user->mail_address = $platform_user->mailAddress;
        $this->brandco_user->profile_image_url = $platform_user->socialAccounts[0]->profileImageUrl;

        $user_service->createUser($this->brandco_user);

        $platform_access_token = $this->getPlatformAccessToken($platform_user);
        if ($platform_access_token) {
            /** @var UserApplicationService $user_application_service */
            $user_application_service = $this->createService('UserApplicationService');
            $user_application_service->createOrUpdateUserApplication($this->brandco_user->id, $this->brand->app_id, $platform_access_token->accessToken, $platform_access_token->refreshToken, "", date('Y-m-d H:i:s'));
        }

    }

    /**
     * ブランドユーザ作成
     */
    private function createBrandUser() {
        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->createService('BrandsUsersRelationService');
        $brands_users_relation = $brands_users_relation_service->getBrandsUsersRelation($this->brand->id, $this->brandco_user->id);
        if (!$brands_users_relation) {

            $brands_users_relation = $brands_users_relation_service->createEmptyBrandsUsersRelation();
            $brands_users_relation->brand_id = $this->brand->id;
            $brands_users_relation->user_id = $this->brandco_user->id;
            $brands_users_relation->no = 0;
            $brands_users_relation->from_kind = BrandsUsersRelationService::FROM_KIND_CAMPAIGN;
            $brands_users_relation_service->createBrandsUsersRelation($brands_users_relation);
        }
    }
}
