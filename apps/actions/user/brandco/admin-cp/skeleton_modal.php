<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class skeleton_modal extends BrandcoGETActionBase {

    protected $ContainerName = 'update_skeleton';
    public $NeedOption = array();

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

    public function validate () {
        $this->Data['cp_id'] = $this->GET['cp_id'];

        $brand = $this->getBrand();
        $validatorService = new CpValidator($brand->id);
        if (!$validatorService->isOwner($this->Data['cp_id'])) {
            return false;
        }
        return true;
    }

    function doAction() {
        $service_factory = new aafwServiceFactory();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');

        $this->Data['cp'] = $cp_flow_service->getCpById($this->Data['cp_id']);
        return 'user/brandco/admin-cp/skeleton_modal.php';
    }
}