<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_schedule_cp extends BrandcoPOSTActionBase
{
    protected $ContainerName = 'api_schedule_cp';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate()
    {
        if (!$this->POST['cp_id']) {
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return false;
        }

        $brand = $this->getBrand();
        $validatorService = new CpValidator($brand->id);
        if (!$validatorService->isOwner($this->POST['cp_id'])) {
            return false;
        }
        return true;
    }

    function doAction()
    {
        /** @var CpFlowService $service */
        $service = $this->createService('CpFlowService');
        $cp = $service->getCpById($this->POST['cp_id']);

        if (!$cp) {
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        if ($cp->status != Cp::STATUS_SCHEDULE) {
            $service->scheduleCp($this->POST['cp_id']);
        } else {
            $service->cancelScheduleCp($this->POST['cp_id']);
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
