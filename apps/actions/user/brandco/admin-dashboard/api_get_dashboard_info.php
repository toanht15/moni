<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.DashboardService');
AAFW::import('jp.aainc.classes.services.UserAttributeService');
AAFW::import('jp.aainc.classes.services.SocialAccountService');

class api_get_dashboard_info extends BrandcoGETActionBase {

    protected $ContainerName = 'api_get_dashboard_info';
    protected $AllowContent = array('JSON');
    public $NeedOption = array();

    public function validate() {
        if($this->all_fan_count === '') {
            $json_data = $this->createAjaxResponse("ng", array());
            $this->assign('json_data', $json_data);
            return false;
        }
        return true;
    }

    function doAction() {
        /** @var $dashboard_service DashboardService */
        $dashboard_service = new DashboardService($this->Data['brand']);
        if($this->date_type == DashboardService::DATE_SUMMARY) {
            list($from_date, $to_date) = $dashboard_service->getSummaryDate($this->summary_date_type, $this->summary_date);
        } else {
            list($from_date, $to_date) = $dashboard_service->getTermDate($this->term_date_type, $this->from_date, $this->to_date);
        }

        $dashboard_info[$this->dashboard_type] = $dashboard_service->getDashboardInfo($this->date_type, $this->dashboard_type, $from_date, $to_date, $this->all_fan_count);

        $json_data = $this->createAjaxResponse("ok", $dashboard_info);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
