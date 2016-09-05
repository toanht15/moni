<?php
require_once dirname(__FILE__) . '/../../config/define.php';

class CloseBrand {
    private $logger;
    private $service_factory;
    private $brand_transaction;
    /** @var BrandContractService brand_contract_service */
    private $brand_contract_service;
    /** @var BrandsUsersRelationService $brands_users_relation_service */
    private $brands_users_relation_service;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();

        $this->brand_transaction = aafwEntityStoreFactory::create('Brands');
        $this->brand_contract_service = $this->service_factory->create('BrandContractService');
        $this->brands_users_relation_service = $this->service_factory->create('BrandsUsersRelationService');
    }

    /**
     * オプトインフラグを下げてクローズステータスへ変更する
     */
    public function closeProcess() {
        $brand_contracts = $this->brand_contract_service->getClosedBrandContract();

        foreach ($brand_contracts as $brand_contract) {
            try {
                $this->brand_transaction->begin();

                $brand_contract->delete_status = BrandContracts::MODE_CLOSED;
                $this->brand_contract_service->updateBrandContract($brand_contract);

                $brand = $brand_contract->getBrand();
                $brands_users_relations = $this->brands_users_relation_service->getBrandsUsersRelationsByBrandId($brand->id);

                foreach ($brands_users_relations as $brands_users_relation) {
                    $this->brands_users_relation_service->changeOptinFlgOnClosedBrand($brands_users_relation);
                }

                $this->brand_transaction->commit();
            } catch (Exception $e) {
                $this->brand_transaction->rollback();
                $this->logger->error('CloseBrand@closeProcess Error');
                $this->logger->error($e);
            }
        }
    }

    /**
     * サイト表示終了条件を満たしている企業のステータスを表示終了へ変更する
     */
    public function updateStatusSiteClose() {
        $brand_contracts = $this->brand_contract_service->getSiteClosedBrandContract();

        foreach ($brand_contracts as $brand_contract) {
            $brand_contract->delete_status = BrandContracts::MODE_SITE_CLOSED;
            $this->brand_contract_service->updateBrandContract($brand_contract);
        }
    }

    /**
     * サイトクローズから指定期間過ぎたサイトに対し
     * ユーザデータを削除しステータスをサイトクローズへ変更する
     */
    public function deleteProcess() {
        $brand_contracts = $this->brand_contract_service->getDeleteUserInfoBrandContract();

        foreach ($brand_contracts as $brand_contract) {
            try {
                $this->brand_transaction->begin();

                $brand_contract->delete_status = BrandContracts::MODE_DATA_DELETED;
                $this->brand_contract_service->updateBrandContract($brand_contract);

                $brand = $brand_contract->getBrand();
                $brands_users_relations = $this->brands_users_relation_service->getBrandsUsersRelationsByBrandId($brand->id);

                foreach ($brands_users_relations as $brands_users_relation) {
                    $this->brands_users_relation_service->withdrawByBrandUserRelation($brands_users_relation);
                }
                $this->brand_transaction->commit();
            } catch (Exception $e) {
                $this->brand_transaction->rollback();
                $this->logger->error('CloseBrand@deleteProcess Error');
                $this->logger->error($e);
            }

        }
    }
}