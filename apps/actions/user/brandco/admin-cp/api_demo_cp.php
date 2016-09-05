<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_demo_cp extends BrandcoPOSTActionBase
{
    protected $ContainerName = 'api_demo_cp';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate()
    {
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

        if ($cp->status == Cp::STATUS_DRAFT) {
            $service->demoCp($this->POST['cp_id']);

        } else {
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $this->getService('CpInstagramHashtagActionService')->initializeInstagramHashtagByCpId($cp->id);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
