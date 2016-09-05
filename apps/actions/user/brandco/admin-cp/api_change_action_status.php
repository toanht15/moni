<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_change_action_status extends BrandcoPOSTActionBase
{
    protected $ContainerName = 'api_change_action_status';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate()
    {
        $brand = $this->getBrand();
        $validatorService = new CpValidator($brand->id);
        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            return false;
        }
        return true;
    }

    function doAction()
    {
        $service = $this->createService('CpFlowService');
        $action = $service->getCpActionById($this->POST['action_id']);
        $action->status = CpAction::STATUS_DRAFT;
        $service->updateCpAction($action);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
