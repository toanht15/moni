<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandContractService extends aafwServiceBase {

    /** @var  BrandContracts $brand_contracts */
    private $brand_contracts;

    public function __construct() {
        $this->brand_contracts = $this->getModel('BrandContracts');
    }

    public function getEmptyBrandContract() {
        return $this->brand_contracts->createEmptyObject();
    }

    public function getBrandContract($brand_contract_id) {
        return $this->brand_contracts->find($brand_contract_id);
    }

    public function getBrandContractByBrandId($brand_id) {
        $filter = array(
            'brand_id' => $brand_id
        );
        return $this->brand_contracts->findOne($filter);
    }

    public function updateBrandContract($brand_contract) {
        $value =  $this->brand_contracts->save($brand_contract);
        BrandInfoContainer::getInstance()->clear($brand_contract->brand_id);
        return $value;
    }

    /**
     * クローズ条件を満たしているステータスが公開中の一覧取得
     * @return aafwEntityContainer|array
     */
    public function getClosedBrandContract() {
        $date = new DateTime();
        $filter = array(
            'conditions' => array(
                'contract_end_date:<' => $date->format('Y-m-d H:i:s'),
                'delete_status' => BrandContracts::MODE_OPEN
            )
        );
        return $this->brand_contracts->find($filter);
    }

    /**
     * サイトクローズ条件を満たしているステータスがクローズモードの一覧取得
     * @return aafwEntityContainer|array
     */
    public function getSiteClosedBrandContract() {
        $date = new DateTime();
        $filter = array(
            'conditions' => array(
                'display_end_date:<' => $date->format('Y-m-d H:i:s'),
                'delete_status' => BrandContracts::MODE_CLOSED
            )
        );
        return $this->brand_contracts->find($filter);
    }

    /**
     * サイトクローズしてから指定期間過ぎている企業一覧取得
     * @return aafwEntityContainer|array
     */
    public function getDeleteUserInfoBrandContract() {
        $filter = array(
            'conditions' => array(
                'display_end_date:<' => date('Y-m-d H:i:s', strtotime(BrandContracts::DATA_MAINTAIN_TERM)),
                'delete_status' => BrandContracts::MODE_SITE_CLOSED
            )
        );
        return $this->brand_contracts->find($filter);
    }

    public function getOperationList() {
        return BrandContract::$OPERATION_LIST;
    }

    public function getForProductionFlgList() {
        return BrandContract::$FOR_PRODUCTION_FLG_LIST;
    }

    public function getSelectablePlan() {
        $selectablePlan = array(
            BrandContract::PLAN_MANAGER_STANDARD => BrandContract::$PLAN_LIST[BrandContract::PLAN_MANAGER_STANDARD],
            BrandContract::PLAN_MANAGER_CP_LITE  => BrandContract::$PLAN_LIST[BrandContract::PLAN_MANAGER_CP_LITE],
            BrandContract::PLAN_PROMOTION_BRAND  => BrandContract::$PLAN_LIST[BrandContract::PLAN_PROMOTION_BRAND],
        );

        return $selectablePlan;
    }

}