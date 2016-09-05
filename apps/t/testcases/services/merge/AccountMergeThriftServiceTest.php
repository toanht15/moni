<?php
AAFW::import('jp.aainc.classes.services.merge.AccountMergeThriftService');
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');

class AccountMergeThriftServiceTest extends BaseTest {

    /** @var  AccountMergeThriftService */
    private $target;


    /**
     * @test
     */
    public function hasOnlyMergeAbleUsingApplication_新モニ関連のusingApplicationしか登録してない場合はtrue() {
        $this->target = $this->getMock(
            'AccountMergeThriftService',
            array('getCore')
        );

        $mockCore = $this->getMock('\Monipla\Core\MoniplaCore', array('getUsingApplications'));

        //getUsingApplicationの返却値を作成
        $thriftUsingApplications = new Thrift_UsingApplications();
        $thritApiStatus = new Thrift_APIStatus();
        $thritApiStatus->status = Thrift_APIStatus::SUCCESS;
        $thriftUsingApplications->result = $thritApiStatus;
        $thrifApplication1 = new Thrift_Application();
        $thrifApplication1->id = 1; //メディア
        $thrifApplication2 = new Thrift_Application();
        $thrifApplication2->id = 10; //brandco
        $thriftUsingApplications->applicationList = array($thrifApplication1,$thrifApplication2);

        $mockCore->expects($this->once())->method('getUsingApplications')->willReturn($thriftUsingApplications);

        $this->target->expects($this->once())->method('getCore')->willReturn($mockCore);

        $result = $this->target->hasOnlyMergeAbleUsingApplication(1);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function hasOnlyMergeAbleUsingApplication_新モニ関連以外のusingApplicationが登録されている場合はfalse() {
        $this->target = $this->getMock(
            'AccountMergeThriftService',
            array('getCore')
        );

        $mockCore = $this->getMock('\Monipla\Core\MoniplaCore', array('getUsingApplications'));

        //getUsingApplicationの返却値を作成
        $thriftUsingApplications = new Thrift_UsingApplications();
        $thritApiStatus = new Thrift_APIStatus();
        $thritApiStatus->status = Thrift_APIStatus::SUCCESS;
        $thriftUsingApplications->result = $thritApiStatus;
        $thrifApplication1 = new Thrift_Application();
        $thrifApplication1->id = 1; //メディア
        $thrifApplication2 = new Thrift_Application();
        $thrifApplication2->id = 2; //新モニ関連以外
        $thriftUsingApplications->applicationList = array($thrifApplication1,$thrifApplication2);


        $mockCore->expects($this->once())->method('getUsingApplications')->willReturn($thriftUsingApplications);

        $this->target->expects($this->once())->method('getCore')->willReturn($mockCore);

        $result = $this->target->hasOnlyMergeAbleUsingApplication(1);
        $this->assertFalse($result);
    }
}
