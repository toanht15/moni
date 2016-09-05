<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class edit_setting_skeleton extends BrandcoGETActionBase {
    protected $ContainerName = 'save_setting_skeleton';

    public $NeedOption = array(BrandOptions::OPTION_CP);
    public $NeedAdminLogin = true;

    private $pageLimited = 5;

    public function validate() {
        return true;
    }

    function doAction() {

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $this->Data['draft_cp'] = $cp_flow_service->getDraftCpsByBrandIdAndArchiveFlg($this->Data['brand']->id, 1, $this->pageLimited, Cp::ARCHIVE_OFF);
        $this->Data['draft_cp_count'] = $cp_flow_service->getDraftCpsCountByBrandIdAndArchiveFlg($this->Data['brand']->id, Cp::ARCHIVE_OFF);

        $this->Data['published_cps'] = $cp_flow_service->getPublishedCampaign($this->Data['brand']->id, 1, $this->pageLimited);
        $this->Data['published_cp_count'] = $cp_flow_service->getPublishedCampaignCountByBrandId($this->Data['brand']->id);

        $brandGlobalSettingService = $this->getService('BrandGlobalSettingService');
        $canUsePaymentModule = $brandGlobalSettingService->getBrandGlobalSetting($this->brand->id, BrandGlobalSettingService::CAN_USE_PAYMENT_MODULE);
        $this->Data['can_use_payment_module'] = $canUsePaymentModule->id ? true : false;

        return 'user/brandco/admin-cp/edit_setting_skeleton.php';
    }
}
