<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class edit_conversion_form extends BrandcoGETActionBase {
    protected $ContainerName = 'edit_conversion_form';
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->deleteErrorSession();
        $this->Data['conversion_id'] = $this->GET['exts'][0];
    }

    public function validate() {
        /** @var ConversionService $conversion_service */
        $conversion_service = $this->createService('ConversionService');
        if ($this->Data['conversion_id'] !== '0') {
            $this->Data['conversion'] = $conversion_service->getConversionById($this->Data['conversion_id']);
            if (!$this->Data['conversion']) {
                return false;
            }

            //所有者チェック
            if($this->brand->id != $this->Data['conversion']->brand_id) {
                return false;
            }

        }else{
            //作成個数上限チェック
            if($conversion_service->isArrivalLimitCount($this->brand->id)) {
                return false;
            }

        }
        $this->Data['cart_types'] = $conversion_service->CART_TYPES;
        return true;
    }

    function doAction() {

        /** @var BrandGlobalSettingService $global_setting_service */
        $global_setting_service = $this->createService('BrandGlobalSettingService');
        $cart_setting = $global_setting_service->getSettingByNameAndBrandId('cart_type', $this->brand->id);
        if ($cart_setting) {
            $this->Data['cart_setting'] = $cart_setting->content;
        }

        if ($this->Data['conversion']) {
            $this->assign('ActionForm', $this->Data['conversion']->toArray());
        }
        return 'user/brandco/admin-settings/edit_conversion_form.php';
    }
}
