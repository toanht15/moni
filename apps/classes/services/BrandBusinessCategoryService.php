<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class BrandBusinessCategoryService extends aafwServiceBase {

    private $brandBusinessCategories;

    public function __construct() {
        $this->brandBusinessCategories = $this->getModel('BrandBusinessCategories');
    }

    public function getCategoryList() {
        return BrandBusinessCategory::$brand_business_category_list;
    }

    public function getSizeList() {
        return BrandBusinessCategory::$brand_business_size_list;
    }

    public function createEmptyBrandBusinessCategory() {
        return $this->brandBusinessCategories->createEmptyObject();
    }

    public function createBrandBusinessCategory($brandId, $category, $size) {
        $brandBusinessCategory = $this->createEmptyBrandBusinessCategory();

        if ($brandId && $category && $size) {
            $brandBusinessCategory->brand_id    = $brandId;
            $brandBusinessCategory->category    = $category;
            $brandBusinessCategory->size        = $size;

            $this->saveBrandBusinessCategory($brandBusinessCategory);
        }

        return $brandBusinessCategory;
    }

    public function getOrCreateBrandBusinessCategoryByBrandId($brandId) {
        $filter = array(
            'brand_id' => $brandId
        );

        if ($brandId) {
            return $this->brandBusinessCategories->findOne($filter);
        } else {
            return $this->createEmptyBrandBusinessCategory();
        }
    }

    public function saveBrandBusinessCategory($brandBusinessCategory) {
        return $this->brandBusinessCategories->save($brandBusinessCategory);
    }
}