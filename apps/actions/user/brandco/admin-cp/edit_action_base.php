<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class edit_action_base extends BrandcoGETActionBase {
    protected $ContainerName = 'save_action_base';

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        $this->Data['action_id'] = $this->GET['exts'][0];

        $brand = $this->getBrand();
        $validatorService = new CpValidator($brand->id);

        if (!$validatorService->isOwnerOfAction($this->Data['action_id'])) {
            return false;
        }

        return true;
    }

    function doAction() {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $this->Data['cp_action'] = $cp_flow_service->getCpActionById($this->Data['action_id']);

        $cp_action_detail = $this->Data['cp_action']->getCpActionDetail();
        $this->ContainerName = $cp_action_detail['form_action'];
        $this->deleteErrorSession();

        $action_manager = $this->Data['cp_action']->getActionManagerClass();

        $cp_action = $action_manager->getCpActions($this->Data['action_id']);
        $group = $cp_flow_service->getCpActionGroupById($cp_action[0]->cp_action_group_id);

        // 発送を持っての編集画面はない
        if ($cp_action[0]->isAnnounceDelivery()) {
            return 404;
        }

        $this->Data['cp_id'] = $group->cp_id;

        $this->assign('ActionForm', $cp_action[1]->toArray());

        $this->Data['status'] = $cp_action[0]->status;

        return 'user/brandco/admin-cp/edit_action_base.php';
    }
}
