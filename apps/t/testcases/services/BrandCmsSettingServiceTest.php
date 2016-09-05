<?php

class BrandCmsSettingServiceTest extends BaseTest {

    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("BrandCmsSettingService");
    }

    public function testBrandCmsSettingSave(){
        $brand = $this->entity("Brands");
        $column = $this->target->createEmptyObject();
        $column->brand_id = $brand->id;
        $column ->category_navi_top_display_flg = 1;
        $this->target->updateBrandCmsSetting($column);

        $result = $this->findOne('BrandCmsSettings', array('brand_id' => $brand->id));
        $this->assertEquals($brand->id, $result->brand_id);
    }
}
