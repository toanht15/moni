<?php
AAFW::import('jp.aainc.classes.services.BrandOptionsService');

class BrandOptionsServiceTest extends BaseTest {

    /** @var BrandOptionsService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create('BrandOptionsService');
    }

    /**
     * @test
     */
    public function test_getEmptyBrandOption() {
        $empty_brand_option = $this->target->getEmptyBrandOption();

        $this->assertNotNull($empty_brand_option);
        $this->assertNull($empty_brand_option->id);
        $this->assertSame('BrandOption', get_class($empty_brand_option));
    }

    /**
     * @test
     */
    public function test_getBrandOptionByBrandIdAndOptionId_引数がNullの場合() {
        $brand_id = null;
        $option_id = null;
        $data = $this->target->getBrandOptionByBrandIdAndOptionId($brand_id, $option_id);

        $this->assertNull($data);
    }

    /**
     * @test
     */
    public function test_getBrandOptionByBrandIdAndOptionId_正常取得() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $brandId = $brand->id;
        $optionId = 1;
        $option = $this->entity('BrandOptions', [
            'brand_id' => $brandId, 
            'option_id' => $optionId
        ]);

        $data = $this->target->getBrandOptionByBrandIdAndOptionId($brandId, $optionId);

        $this->assertNotNull($data);
        $this->assertEquals($brandId, $data->brand_id);
        $this->assertEquals($optionId, $data->option_id);
    }

    /**
     * @test
     */
    public function test_updateBrandOptions_登録() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $brand_options = [
            BrandOptions::OPTION_CMS,
            BrandOptions::OPTION_FAN_LIST,
            BrandOptions::OPTION_CRM,
            BrandOptions::OPTION_DASHBOARD,
        ];
        $this->target->updateBrandOptions($brand->id, $brand_options);

        $this->assertEquals(4, $this->countEntities('BrandOptions', [
            'brand_id' => $brand->id
        ]));
    }

    /**
     * @test
     */
    public function test_updateBrandOptions_削除() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        // 新規登録
        $brand_options = [
            BrandOptions::OPTION_CMS,
            BrandOptions::OPTION_FAN_LIST,
            BrandOptions::OPTION_CRM,
            BrandOptions::OPTION_DASHBOARD,
        ];
        $this->target->updateBrandOptions($brand->id, $brand_options);
        $this->assertEquals(4, $this->countEntities('BrandOptions', [
            'brand_id' => $brand->id
        ]));
        // 削除
        $brand_options = [];
        $this->target->updateBrandOptions($brand->id, []);
        $this->assertEquals(0, $this->countEntities('BrandOptions', [
            'brand_id' => $brand->id
        ]));
    }
}
