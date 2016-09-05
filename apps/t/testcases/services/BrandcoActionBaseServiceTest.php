<?php

require_once preg_replace( '#/$#', '', AAFW_DIR ) . '/t/testcases/UTTestAction.php';

AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandcoActionBaseServiceTest extends BaseTest {

    /** @var  UTTestAction $target */
    private $target;

    public function setUp() {
        $this->target = new UTTestAction();
        $this->target->result = "SUCCESS";
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
        $instance = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($instance, "cache", null);
        $this->setPrivateFieldValue($instance, "brand", null);
    }

    public function testGetBrand01_whenExist() {
        $brand = $this->entity("Brands");
        BrandInfoContainer::getInstance()->initialize($brand);

        $result = $this->target->getBrand();

        $this->assertEquals($brand, $result);
    }

    public function testGetBrand02_whenAbsent() {
        $result = $this->target->getBrand();
        $this->assertNull($result);
    }

    public function testDoService01_whenClosed() {
        $brand = $this->entity("Brands");
        $this->entity("BrandContracts", array("brand_id" => $brand->id, "contract_end_date" => "1970/01/01 00:00:00", "display_end_date" => "2999/12/31 00:00:00"));
        BrandInfoContainer::getInstance()->initialize($brand);

        $result = $this->target->doService();

        $this->assertEquals("redirect: http://brandcotest.com/closed", $result);
    }

    public function testDoService02_whenModeSiteClosed() {
        $brand = $this->entity("Brands");
        $this->entity("BrandContracts", array("brand_id" => $brand->id, "contract_end_date" => "2999/12/31 00:00:00", "display_end_date" => "1970/01/01 00:00:00"));
        BrandInfoContainer::getInstance()->initialize($brand);

        $result = $this->target->doService();

        $this->assertEquals("404", $result);
    }

    public function testDoService03_whenSuccess() {
        $brand = $this->entity("Brands");
        $this->entity("BrandContracts", array("brand_id" => $brand->id, "contract_end_date" => "2999/01/01 00:00:00", "display_end_date" => "2999/12/31 00:00:00"));
        $this->entity("BrandPageSettings", array("brand_id" => $brand->id, "public_flg" => 1));
        BrandInfoContainer::getInstance()->initialize($brand);

        $result = $this->target->doService();

        $this->assertEquals("SUCCESS", $result);
    }
}