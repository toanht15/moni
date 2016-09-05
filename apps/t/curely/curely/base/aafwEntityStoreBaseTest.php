<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class aafwEntityStoreBaseTest extends BaseTest {

    /**
     * create test
     * @test
     */
    public function test_findOne() {
        $brands = aafwEntityStoreFactory::create('Brands');
        $brand = $brands->findOne();
        $this->assertThat($brand->id, $this->equalTo('1'));
    }

    public function test_delete() {
        $brands = aafwEntityStoreFactory::create('Brands');
        $brand = $this->entity('brands');

        $brands->delete($brand);
        $deleted_brand = $brands->findOne($brand->id);
        $this->assertNull($deleted_brand);
    }

    public function test_deleteLogical() {
        $brands = aafwEntityStoreFactory::create('Brands');
        $brand = $this->entity('brands');

        $brands->deleteLogical($brand);
        $deleted_brand = $brands->findOne($brand->id);
        $this->assertNull($deleted_brand);
    }

    public function test_deletePysical() {
        $brands = aafwEntityStoreFactory::create('Brands');
        $brand = $this->entity('brands');

        $brands->deletePhysical($brand);
        $deleted_brand = $brands->findOne($brand->id);
        $this->assertNull($deleted_brand);
    }
}
