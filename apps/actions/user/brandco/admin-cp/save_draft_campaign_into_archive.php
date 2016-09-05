<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_draft_campaign_into_archive extends BrandcoPOSTActionBase {

    protected $ContainerName = 'save_setting_skeleton';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => 'edit_setting_skeleton',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'cp_id' => array(
            'required' => true
        )
    );

    public function validate() {
        $this->Data['brand'] = $this->getBrand();
        $validatorService = new CpValidator($this->Data['brand']->id);
        if (!$validatorService->isOwner($this->POST['cp_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }
        return true;
    }

    function doAction() {

        $cp_flow_service = $this->createService('CpFlowService');
        $cp = $cp_flow_service->getCpById($this->POST['cp_id']);
        $cp->archive_flg = Cp::ARCHIVE_ON;

        $cp_flow_service->updateCp($cp);

        $stamp_rally_service = $this->createService('StaticHtmlStampRallyService');
        $stamp_rally_service->deleteStampRallyCampaignByCpId($cp->id);
        
        $this->Data['saved'] = 1;
        $return = 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_setting_skeleton');
        return $return;
    }
}
