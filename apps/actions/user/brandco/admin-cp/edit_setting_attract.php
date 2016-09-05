<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class edit_setting_attract extends BrandcoGETActionBase {
    protected $ContainerName = 'save_setting_attract';

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

    public function validate() {
        $this->Data['cp_id'] = $this->GET['exts'][0];

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $this->Data['cp'] = $cp_flow_service->getCpById($this->Data['cp_id']);
        if ($this->Data['cp']->join_limit_flg == cp::JOIN_LIMIT_ON) {
            return "404";
        }

        $this->Data['brand'] = $this->getBrand();
        $validatorService = new CpValidator($this->Data['brand']->id);
        if (!$validatorService->isOwner($this->Data['cp_id'])) {
            return false;
        }

        return true;
    }

    function doAction() {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $this->assign('ActionForm', $this->Data['cp']->toArray());

        $first_group = $cp_flow_service->getCpActionGroupsByCpId($this->Data['cp']->id)->current();
        $first_action = $cp_flow_service->getCpActionsByCpActionGroupId($first_group->id)->current();
        $first_action_detail = $first_action->getCpActionDetail();

        $this->Data['action_id']    = $first_action->id;
        $this->Data['action_title'] = $first_action_detail['title'];
        $this->Data['cp_link']      = $this->Data['cp']->getUrl();
        $this->Data['status']       = $this->Data['cp']->fix_attract_flg;
        $this->Data['CpStatus']     = $this->Data['cp']->getStatus();
        $this->Data['isManager']    = $this->isLoginManager();

        return 'user/brandco/admin-cp/edit_setting_attract.php';
    }

    public static function canEditCp($cp_status) {
        return in_array($cp_status, array(Cp::CAMPAIGN_STATUS_OPEN, Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE, Cp::CAMPAIGN_STATUS_CLOSE));
    }
}
