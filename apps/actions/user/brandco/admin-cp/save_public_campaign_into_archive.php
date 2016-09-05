<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class save_public_campaign_into_archive extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    /** @var CpFlowService $cp_flow_service */
    public $cp_flow_service;

    public function validate() {

        $this->cp_flow_service = $this->createService('CpFlowService');
        $validatorService = new CpValidator($this->getBrand()->id);
        if (!$validatorService->isOwner($this->GET['exts'][0])) {
            return false;
        }
        return true;
    }

    function doAction() {
        $cp = $this->cp_flow_service->getCpById($this->GET['exts'][0]);
        if ($cp->type == Cp::TYPE_CAMPAIGN && $cp->getStatus() != Cp::CAMPAIGN_STATUS_CLOSE) {
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'public_cps', array(), array('p' => $this->p, 'type' => $this->type, 'mid' => 'failed'));
        }

        $cp->archive_flg = !$cp->archive_flg;
        $this->cp_flow_service->updateCp($cp);

        return 'redirect: ' . Util::rewriteUrl('admin-cp', 'public_cps', array(), array('p' => $this->p, 'type' => $this->type, 'mid' => 'updated', 'archive' => $cp->archive_flg ? 0 : 1));
    }
}
