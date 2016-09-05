<?php
AAFW::import('jp.aainc.actions.user.api.withdraw');

class WithdrawTest extends BaseTest {

    public function testDoService01_success() {
        $target = new withdraw();
        $target->doThisFirst();

        $mock = new WithdrawTest_MoniplaCoreMock();
        $mock->resolveSignedRequest = 1;
        $target->setMoniplaCore($mock);
        $result = $target->doService();
        $data = $target->getData();

        $expected = array(
            "dummy.php",
            array(
                "json_data" => array(
                    "result" => "ok",
                    "data" => array(),
                    "errors" => array(),
                    "html" => ""
                )
            )
        );
        $this->assertEquals(
            json_encode($expected, JSON_PRETTY_PRINT),
            json_encode(array($result, $data), JSON_PRETTY_PRINT));
    }

    public function testDoService02_failureNoRequest() {
        $target = new withdraw();
        $target->doThisFirst();
        $target->setMoniplaCore(new WithdrawTest_MoniplaCoreMock());
        $result = $target->doService();
        $data = $target->getData();

        $expected = array(
            "dummy.php",
            array(
                "json_data" => array(
                    "result" => "ng",
                    "data" => array(),
                    "errors" => array(),
                    "html" => ""
                )
            )
        );
        $this->assertEquals(
            json_encode($expected, JSON_PRETTY_PRINT),
            json_encode(array($result, $data), JSON_PRETTY_PRINT));
    }

    public function testDoService03_failureThrowException() {
        $target = new withdraw();
        $target->doThisFirst();
        $mock = new WithdrawTest_MoniplaCoreMock();
        $mock->throw = true;
        $target->setMoniplaCore($mock);
        $result = $target->doService();
        $data = $target->getData();

        $expected = array(
            "dummy.php",
            array(
                "json_data" => array(
                    "result" => "ng",
                    "data" => array(),
                    "errors" => array(),
                    "html" => ""
                )
            )
        );
        $this->assertEquals(
            json_encode($expected, JSON_PRETTY_PRINT),
            json_encode(array($result, $data), JSON_PRETTY_PRINT));
    }
}

class WithdrawTest_MoniplaCoreMock {

    public $throw;

    public $resolveSignedRequest;

    public function resolveSignedRequest() {
        if ($this->throw) {
            throw new aafwException("HOGE!");
        }
        return $this->resolveSignedRequest;
    }
}