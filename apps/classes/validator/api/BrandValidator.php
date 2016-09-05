<?php

AAFW::import('jp.aainc.classes.validator.BaseValidator');

class BrandValidator extends BaseValidator {

    private $brand_id;
    private $brand;
    private $service_factory;

    public function __construct($brand_id) {
        parent::__construct();
        $this->brand_id = $brand_id;
        $this->service_factory = new aafwServiceFactory();
    }

    public function validate() {

        // brand_idのチェック
        if (trim($this->brand_id) === '') {
            $this->errors['brand_id'][] = "brandが存在しません";
            return;
        }

        /** @var BrandService $brand_service */
        $brand_service = $this->service_factory->create('BrandService');
        $this->brand = $brand_service->getBrandById($this->brand_id);

        // ブランドの存在チェック
        if (!$this->brand->id) {
            $this->errors['brand_id'][] = "brandが存在しません";
            return;
        }

        /** @var BrandPageSettingService $brand_page_setting_service */
        $brand_page_setting_service =  $this->service_factory->create('BrandPageSettingService');

        $brand_page_setting = $brand_page_setting_service->getPageSettingsByBrandId($this->brand->id);

        // ページの公開状態のチェック
        if ($brand_page_setting->public_flg == BrandPageSettingService::STATUS_NON_PUBLIC) {
            $this->errors['brand_id'][] = "brandが存在しません";
            return;
        }
    }

    /**
     * @return mixed
     */
    public function getBrand() {
        return $this->brand;
    }
}
