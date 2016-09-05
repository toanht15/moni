<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class BrandCmsSettingService extends aafwServiceBase {

    /** @var BrandCmsSettings $brand_cms_settings */
    private $brand_cms_settings;

	public function __construct() {
        $this->brand_cms_settings = $this->getModel("BrandCmsSettings");
    }

    public function updateBrandCmsSetting($brand_cms_setting) {
        return $this->brand_cms_settings->save($brand_cms_setting);
    }

    public function getBrandCmsSettingByBrandId($brand_id) {
        $filter = array(
            'brand_id' => $brand_id
        );
        return $this->brand_cms_settings->findOne($filter);
    }

    public function createEmptyObject() {
        return $this->brand_cms_settings->createEmptyObject();
    }
}