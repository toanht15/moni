<?php
AAFW::import ('jp.aainc.classes.services.BrandsUsersSearchInfoService');

class BrandsUsersSearchInfoServiceTest extends BaseTest {

    /** @var  BrandContractService $target */
    private $target;
//    private $brand_id;

    public function setUp() {
        $this->target = aafwServiceFactory::create("BrandsUsersSearchInfoService");
//        $this->brand_id = 1;
    }

    public function test_getTargetDate_notArgv() {
        $argv = null;
        list($from_date, $to_date) = $this->target->getTargetDate($argv);
        $expect_from_date = date('Y-m-d H:i:s', strtotime('yesterday 00:00:00'));
        $expect_to_date = date('Y-m-d H:i:s', strtotime('yesterday 23:59:59'));
        $this->assertEquals(array($from_date, $to_date), array($expect_from_date, $expect_to_date));
    }

    public function test_getTargetDate_existArgv() {
        $argv = array('from_date'=>'2015-07-01','to_date'=>'2015-07-03');
        list($from_date, $to_date) = $this->target->getTargetDate($argv);
        $expect_from_date = date('Y-m-d H:i:s', strtotime('20150701 00:00:00'));
        $expect_to_date = date('Y-m-d H:i:s', strtotime('20150703 23:59:59'));
        $this->assertEquals(array($from_date, $to_date), array($expect_from_date, $expect_to_date));
    }

    public function test_getTargetDate_fromDateError() {
        $argv = array('from_date'=>'2015','to_date'=>'2015-07-03');
        list($from_date, $to_date) = $this->target->getTargetDate($argv);
        $expect_from_date = null;
        $expect_to_date = date('Y-m-d H:i:s', strtotime('20150703 23:59:59'));
        $this->assertEquals(array($from_date, $to_date), array($expect_from_date, $expect_to_date));
    }

    public function test_getTargetDate_toDateError() {
        $argv = array('from_date'=>'2015-07-01','to_date'=>'2015');
        list($from_date, $to_date) = $this->target->getTargetDate($argv);
        $expect_from_date = date('Y-m-d H:i:s', strtotime('20150701 00:00:00'));
        $expect_to_date = null;
        $this->assertEquals(array($from_date, $to_date), array($expect_from_date, $expect_to_date));
    }
}