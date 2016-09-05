<?php

AAFW::import('jp.aainc.widgets.classes.CriteoTracker');
AAFW::import('jp.aainc.classes.util.Hash');
class CriteoTrackerTest extends BaseTest {

    /** @var  CriteoTracker */
    private $target;

    protected function setUp() {
        $this->target = $this->getMockBuilder('CriteoTracker')
            ->disableOriginalConstructor()
            ->setMethods(array('getPlatformUserInfo'))
            ->getMock();
    }


    /**
     * @test
     */
    public function test_正常パターン() {

        $userInfo = new stdClass();
        $result = new stdClass();
        $result->status = Thrift_APIStatus::SUCCESS;
        $userInfo->id = 2;
        $userInfo->result = $result;
        $userInfo->mailAddress = "email@email.com";
        $this->target->expects($this->once())
            ->method('getPlatformUserInfo')
            ->will($this->returnValue($userInfo));

        $resultParams = $this->target->doService(array('platform_user_id'=>1));
        $this->assertEquals("4f64c9f81bb0d4ee969aaf7b4a5a6f40",$resultParams['md5MailAddress']);
    }


    /**
     * @test
     */
    public function test_引数で渡されたUserInfoが空のときは空() {
        $resultParams = $this->target->doService(array());
        $this->assertEquals("",$resultParams['md5MailAddress']);
    }


    /**
     * @test
     */
    public function test_引数で渡されたplatform_user_idが数値じゃない場合は空() {
        $resultParams = $this->target->doService(array('platform_user_id'=>"is not numeric"));
        $this->assertEquals("",$resultParams['md5MailAddress']);
    }

    /**
     * @test
     */
    public function test_getPlatformUserInfoでFAILの場合は空() {

        $userInfo = new stdClass();
        $result = new stdClass();
        $result->status = Thrift_APIStatus::FAIL;
        $userInfo->id = 2;
        $userInfo->result = $result;
        $userInfo->mailAddress = "email@email.com";
        $this->target->expects($this->once())
            ->method('getPlatformUserInfo')
            ->will($this->returnValue($userInfo));

        $resultParams = $this->target->doService(array('platform_user_id'=>1));
        $this->assertEquals("",$resultParams['md5MailAddress']);
    }


}