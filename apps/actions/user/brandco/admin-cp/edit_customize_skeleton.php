<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.Cp');
AAFW::import('jp.aainc.classes.services.SegmentService');

class edit_customize_skeleton extends BrandcoGETActionBase {
    protected $ContainerName = 'save_setting_skeleton';

    public $NeedOption = array(BrandOptions::OPTION_CRM , BrandOptions::OPTION_CP);
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->Data['type'] = $this->GET['type'];
        return true;
    }

    public function validate() {
        if (!$this->Data['type']){
            return false;
        }elseif( !in_array($this->Data['type'], array_keys(cp::$cp_type_array))){
            return false;
        }

        if ($this->Data['type'] == Cp::TYPE_CAMPAIGN) {
            if (!in_array($this->GET['shipping'], array_keys(CpPresentCreator::$shipping_address_type))) return '404';

            if (!(in_array($this->GET['basic_type'], Cp::$basic_skeleton_type) || in_array($this->GET['basic_type'], Cp::$template_skeleton_type) || in_array($this->GET['basic_type'], Cp::$permanent_skeleton_type))) return '404';

            if ($this->GET['announce'] != CpCreator::ANNOUNCE_NON_INCENTIVE && !in_array($this->GET['announce'], array_keys(CpNewSkeletonCreator::$announce_type))) return '404';

            $this->Data['basic_type'] = $this->GET['basic_type'];
            $this->Data['shipping_type'] = $this->GET['shipping'];
            $this->Data['join_limit_flg']   = $this->GET['join_limit_flg'];
            $this->Data['announce_type'] = $this->GET['announce'];
        } elseif ($this->Data['type'] == Cp::TYPE_MESSAGE) {
            $this->Data['show_segment_message_action_alert'] = $this->getBrandSession(SegmentService::SEGMENT_CONDITION_SESSION_KEY) ? true : false;
        }

        return true;
    }

    function doAction() {
        $brandGlobalSettingService = $this->getService('BrandGlobalSettingService');
        $canUsePaymentModule = $brandGlobalSettingService->getBrandGlobalSetting($this->brand->id, BrandGlobalSettingService::CAN_USE_PAYMENT_MODULE);
        $this->Data['can_use_payment_module'] = $canUsePaymentModule->id ? true : false;
        
        return 'user/brandco/admin-cp/edit_customize_skeleton.php';
    }
}
