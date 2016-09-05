<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class inquiry_settings_form extends BrandcoGETActionBase {
    protected $ContainerName = 'inquiry_settings';
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {

        return true;
    }

    function doAction() {
        /** @var InquiryBrandService $inquiry_brand_service */
        $inquiry_brand_service = $this->createService('InquiryBrandService');

        $this->Data['inquiry_brand'] = $inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND, array('brand_id' => $this->brand->id));
        $this->Data['inquiry_brand_receivers'] = $inquiry_brand_service->getRecords(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND_RECEIVERS, array(
            'inquiry_brand_id' => $this->Data['inquiry_brand']->id
        ));

        $this->Data['n_inquiry_brand_receivers'] = $this->Data['inquiry_brand_receivers'] ? count($this->Data['inquiry_brand_receivers']->toArray()) : 0;

        return 'user/brandco/admin-settings/inquiry_settings_form.php';
    }
}
