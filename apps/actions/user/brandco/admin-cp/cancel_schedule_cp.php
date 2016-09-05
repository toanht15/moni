<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class cancel_schedule_cp extends BrandcoPOSTActionBase {
    protected $ContainerName = 'cancel_schedule_cp';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => 'edit_setting_basic/{cp_id}',
    );

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    /** @var CpFlowService $cp_flow_service */
    protected $cp_flow_service;
    protected $cp;

    protected $ValidatorDefinition = array(
        "cp_id" => array(
            "required" => true
        )
    );

    public function validate() {
        $this->Data['brand'] = $this->getBrand();
        $validatorService = new CpValidator($this->Data['brand']->id);
        if (!$validatorService->isOwner($this->POST['cp_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }
        $this->cp_flow_service = $this->createService('CpFlowService');
        $this->cp = $this->cp_flow_service->getCpById($this->POST['cp_id']);
        if ($this->cp->getStatus() != Cp::CAMPAIGN_STATUS_SCHEDULE) {
            return false;
        }

        return true;
    }

    function doAction() {
        $this->cp->status = Cp::STATUS_DRAFT;

        $this->cp_flow_service->updateCp($this->cp);

        $this->Data['saved'] = 1;

        return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_setting_basic', array($this->POST['cp_id']), array('mid'=>'action-draft'));
    }
}
