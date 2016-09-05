<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class SalesManagerService extends aafwServiceBase {

    public function __construct() {
        $this->salesmanagers = $this->getModel("SalesManagers");
    }

    public function getAllSalesManager() {
        return $this->salesmanagers->find(array());
    }

    public function addSalesManager($brandInfo, $brand) {

        $addManager = $this->createEmptySalesManager();
        $addManager->brand_id = $brand->id;
        $addManager->sales_manager_id = $brandInfo['sales_manager'];
        $this->saveSalesManagerInfo($addManager);
    }

    public function saveSalesManagerInfo($saveSalesManagerInfo) {
        $this->salesmanagers->save($saveSalesManagerInfo);
    }

    public function createEmptySalesManager() {
        return $this->salesmanagers->createEmptyObject();
    }


    public function getSalesManagerInfoByBrandId($id) {
        $filter = array(
            'brand_id' => $id,
        );
        return $this->salesmanagers->findOne($filter);
    }

    public function updateSalesManagerList($salesManager) {
        $this->salesmanagers->save($salesManager);
    }
}