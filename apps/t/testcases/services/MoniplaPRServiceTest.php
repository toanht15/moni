<?php

AAFW::import('jp.aainc.classes.services.MoniplaPRService');

class MoniplaPRServiceTest extends BaseTest {

    // app.ymlのMoniplaPR.MoniplaLinkCpIdを参照している

    /** @var MoniplaPRService */
    private $target;

    public function setUp() {
        $this->target = $this->getMockBuilder('MoniplaPRService')
            ->setMethods(array('findSynCp','isSmartPhone'))
            ->getMock();
    }

    public function testCanDisplayMoniplaLink01_disallowed() {
        $brand = $this->entity('Brands', array('monipla_pr_allow_type' => Brand::MONIPLA_PR_ALLOW_TYPE_DISALLOWED));
        $cp_id = '1';
        $fid = 'test';

        $result = $this->target->canDisplayMoniplaLink($brand, $cp_id, $fid);
        $this->assertEquals(false, $result);
    }

    public function testCanDisplayMoniplaLink02_alwaysAllowed() {
        $brand = $this->entity('Brands', array('monipla_pr_allow_type' => Brand::MONIPLA_PR_ALLOW_TYPE_ALWAYS_ALLOWED));
        $cp_id = '1';
        $fid = 'test';

        $result = $this->target->canDisplayMoniplaLink($brand, $cp_id, $fid);
        $this->assertEquals(true, $result);
    }

    public function testCanDisplayMoniplaLink03_whenFidIsFromMedia() {
        $brand = $this->entity('Brands', array('monipla_pr_allow_type' => Brand::MONIPLA_PR_ALLOW_TYPE_NOT_SET));
        $cp_id = '1';
        $fid = 'mpsplpc';

        $result = $this->target->canDisplayMoniplaLink($brand, $cp_id, $fid);
        $this->assertEquals(true, $result);
    }

    public function testCanDisplayMoniplaLink04_whenFidIsNotFromMedia() {
        $brand = $this->entity('Brands', array('monipla_pr_allow_type' => Brand::MONIPLA_PR_ALLOW_TYPE_NOT_SET));
        $cp_id = '1';
        $fid = 'test';

        $result = $this->target->canDisplayMoniplaLink($brand, $cp_id, $fid);
        $this->assertEquals(false, $result);
    }

    public function testCanDisplayMoniplaLink05_whenCpIsLessThanMoniplaLinkCpId() {
        $brand = $this->entity('Brands', array('monipla_pr_allow_type' => Brand::MONIPLA_PR_ALLOW_TYPE_NOT_SET));
        // app.ymlのMoniplaLinkCpIdより小さい
        $cp_id = '1';
        $fid = 'test';

        $result = $this->target->canDisplayMoniplaLink($brand, $cp_id, $fid);
        $this->assertEquals(false, $result);
    }

    public function testCanDisplayMoniplaLink05_whenCpIsEqualsToMoniplaLinkCpId() {
        $brand = $this->entity('Brands', array('monipla_pr_allow_type' => Brand::MONIPLA_PR_ALLOW_TYPE_NOT_SET));
        // app.ymlのMoniplaLinkCpIdと同値
        $cp_id = '2';
        $fid = 'test';

        $result = $this->target->canDisplayMoniplaLink($brand, $cp_id, $fid);
        $this->assertEquals(false, $result);
    }

    public function testCanDisplayMoniplaLink05_whenCpIsGreaterThanMoniplaLinkCpId() {
        $brand = $this->entity('Brands', array('monipla_pr_allow_type' => Brand::MONIPLA_PR_ALLOW_TYPE_NOT_SET));
        // app.ymlのMoniplaLinkCpIdより大きい
        $cp_id = '3';
        $fid = 'test';

        $result = $this->target->canDisplayMoniplaLink($brand, $cp_id, $fid);
        $this->assertEquals(true, $result);
    }

}