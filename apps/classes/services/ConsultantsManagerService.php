<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class ConsultantsManagerService extends aafwServiceBase {

    public function __construct() {
        $this->consultants_managers = $this->getModel("ConsultantsManagers");
    }

    public function getAllConsultantsManager() {
        return $this->consultants_managers->find(array());
    }

    public function addConsultansManager($brandInfo, $brand) {

        $addManager = $this->createEmptyConsuntantsManager();
        $addManager->brand_id = $brand->id;
        if($brandInfo['consultants_manager'] == 0){
            $addManager->consultants_manager_id = 0;
        }else{
            $addManager->consultants_manager_id = $brandInfo['consultants_manager'];
        }
        $this->saveConsultantsManagerInfo($addManager);
    }

    public function saveConsultantsManagerInfo($saveConsultantsManagerInfo) {
        $this->consultants_managers->save($saveConsultantsManagerInfo);
    }

    public function createEmptyConsuntantsManager() {
        return $this->consultants_managers->createEmptyObject();
    }

    public function getConsultantsManagerByBrandId($id) {
        $filter = array(
            'brand_id' => $id,
        );
        return $this->consultants_managers->findOne($filter);
    }

    public function updateConsultantsManagerList($consultantsManager) {
        $this->consultants_managers->save($consultantsManager);
    }

}