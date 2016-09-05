<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class BrandTransactionService extends aafwServiceBase {

    /** @var  BrandTransactions $brands */
    protected $brand_transactions;

    public function __construct() {
        $this->brand_transactions = $this->getModel("BrandTransactions");
    }

    /**
     * @param $brand_id
     */
    public function createBrandTransactions($brand_id) {
        $brand_transaction = $this->brand_transactions->createEmptyObject();
        $brand_transaction->brand_id = $brand_id;
        $this->brand_transactions->save($brand_transaction);
    }

    /**
     * ロック
     * @param $brand_id
     * @return mixed
     */
    public function getBrandTransactionByIdForUpdate($brand_id){
        $filter = array(
            'brand_id' => $brand_id,
            'for_update' => true
        );
        return $this->brand_transactions->findOne($filter);
    }
}