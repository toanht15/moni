<?php
AAFW::import('jp.aainc.classes.services.ApplicationService');

class ApplicationServiceTest extends BaseTest {

    public function testGetClientId01_WhenArgIsNull() {
        $actual = ApplicationService::getClientId(null);

        $this->assertEquals('brandco', $actual);
    }

    public function testGetClientId02_WhenPlatform() {
        $brand = $this->entity('Brands', array('app_id' => ApplicationService::PLATFORM));

        $actual = ApplicationService::getClientId($brand);

        $this->assertEquals(ApplicationService::CLIENT_ID_PLATFORM, $actual);
    }

    public function testGetClientId03_WhenBrandco() {
        $brand = $this->entity('Brands', array('app_id' => ApplicationService::BRANDCO));

        $actual = ApplicationService::getClientId($brand);

        $this->assertEquals(ApplicationService::CLIENT_ID_BRANDCO, $actual);
    }

    public function testGetClientId04_WhenMonipla() {
        $brand = $this->entity('Brands', array('app_id' => ApplicationService::MONIPLA));

        $actual = ApplicationService::getClientId($brand);

        $this->assertEquals(ApplicationService::CLIENT_ID_COMCAMPAIGN, $actual);
    }

    public function testGetClientId05_WhenDomainMappingDmTest() {
        $brand = $this->entity('Brands', array('app_id' => ApplicationService::DOMAIN_MAPPING_DM_TEST));

        $actual = ApplicationService::getClientId($brand);

        $this->assertEquals('dm_test', $actual);
    }

    public function testGetClientId06_WhenDomainMappingKose() {
        $brand = $this->entity('Brands', array('app_id' => ApplicationService::DOMAIN_MAPPING_KOSE));

        $actual = ApplicationService::getClientId($brand);

        $this->assertEquals('dm_kose', $actual);
    }

    public function testGetClientId07_WhenDomainMappingIsehan() {
        $brand = $this->entity('Brands', array('app_id' => ApplicationService::DOMAIN_MAPPING_ISEHAN));

        $actual = ApplicationService::getClientId($brand);

        $this->assertEquals('dm_isehan', $actual);
    }

    public function testGetClientId09_WhenDomainMappingGDO() {
        $brand = $this->entity('Brands', array('app_id' => ApplicationService::DOMAIN_MAPPING_GDO));

        $actual = ApplicationService::getClientId($brand);

        $this->assertEquals('dm_gdo', $actual);
    }
}