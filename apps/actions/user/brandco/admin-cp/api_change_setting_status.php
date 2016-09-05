<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_change_setting_status extends BrandcoPOSTActionBase
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
        if (!$validatorService->isOwner($this->POST['cp_id'])) {
            return false;
        }
        if (!$this->POST['setting']) {
            return false;
        }
        return true;
    }

    function doAction()
    {
        $cp_service = $this->createService('CpFlowService');
        $cp = $cp_service->getCpById($this->POST['cp_id']);
        if ($this->POST['setting'] == 1) {
            $cp->fix_basic_flg = Cp::SETTING_DRAFT;
        } elseif ($this->POST['setting'] == 2) {
            $cp->fix_attract_flg = Cp::SETTING_DRAFT;
        }
        $cp_service->updateCp($cp);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
