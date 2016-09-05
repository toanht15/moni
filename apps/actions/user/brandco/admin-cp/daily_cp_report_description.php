<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class daily_cp_report_description extends BrandcoGETActionBase {

    protected $ContainerName = 'daily_cp_report_description';

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    private $cp;

    public function doThisFirst() {
        $cp_id = $this->GET['cp_id'];
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService("CpFlowService");
        $this->cp = $cp_flow_service->getCpById($cp_id);
    }

    public function validate() {
        if(!$this->cp || $this->cp->brand_id != $this->getBrand()->id) {
            return false;
        }

        return true;
    }

    public function doAction() {
        $this->Data['cp'] = $this->cp;

        if ($this->cp->reference_url && preg_match('/\/page\//', $this->cp->reference_url)) {
            $this->Data['is_use_lp_page'] = true;
        }

        return 'user/brandco/admin-cp/daily_cp_report_description.php';
    }
}