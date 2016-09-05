<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_cancel_demo extends BrandcoPOSTActionBase
{
    protected $ContainerName = 'api_cancel_demo';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate()
    {
        $brand = $this->getBrand();

        /** @var CpFlowService $service */
        $service = $this->createService('CpFlowService');
        $cp = $service->getCpById($this->POST['cp_id']);
        if ($cp && $cp->status != Cp::STATUS_DEMO) {
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);

            return false;
        }

        $validatorService = new CpValidator($brand->id);
        if (!$validatorService->isOwner($this->POST['cp_id'])) {

            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);

            return false;
        }
        return true;
    }

    function doAction()
    {
        /** @var CpFlowService $service */
        $service = $this->createService('CpFlowService');

        $model = $service->getCpStore();
        try {
            $model->begin();
            $service->cancelDemoByCpId($this->POST['cp_id']);
            $model->commit();
        } catch (Exception $e) {
            $model->rollback();
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error("api_cancel_demo_data#doAction false cp_id=".$this->POST['cp_id']);
            $logger->error($e);

            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $json_data = $this->createAjaxResponse("ok", array("cp_id" => $this->POST['cp_id']));
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
