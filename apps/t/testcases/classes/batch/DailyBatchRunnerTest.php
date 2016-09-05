<?php

AAFW::import('jp.aainc.classes.batch.DailyBatchRunner');

class DailyBatchRunnerTest extends BaseTest {
    // 微妙だけど大体のバッチがechoして終わりって感じで作られてるのでバッファをキャプチャしてテストする

    /** @var DailyBatchRunner $target */
    private $target = null;

    public function setUp() {
        $this->target = $this->getMockBuilder('DailyBatchRunner')
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $this->target->setClazz('DailyBatchRunnerTest_Dummy');
    }

    public function testExecuteProcess_whenDateIsInvalidFormat() {

        $this->target->setArgv([
            'date' => 'aaa'
        ]);
        $this->target->executeProcess();
        $this->expectOutputString("Invalid argument: The 'date' does not match the format Y-m-d.");
    }

    public function testExecuteProcess_whenSinceIsInvalidFormat() {

        $this->target->setArgv([
            'since' => 'aaa'
        ]);
        $this->target->executeProcess();
        $this->expectOutputString("Invalid argument: The 'since' does not match the format Y-m-d.");
    }

    public function testExecuteProcess_whenUntilIsInvalidFormat() {

        $this->target->setArgv([
            'until' => 'aaa'
        ]);
        $this->target->executeProcess();
        $this->expectOutputString("Invalid argument: The 'until' does not match the format Y-m-d.");
    }

    public function testExecuteProcess_whenUntilIsBeforeSince() {

        $this->target->setArgv([
            'since' => '2015-01-02',
            'until' => '2015-01-01'
        ]);
        $this->target->executeProcess();
        $this->expectOutputString("Invalid argument: The 'since' must be a date before 'until'.");
    }

    public function testExecuteProcess_notSpecify() {
        $expected = date('Y-m-d', strtotime('-1 day'));
        $this->target->setArgv([
        ]);
        $this->target->executeProcess();
        $this->expectOutputString($expected);
    }

    public function testExecuteProcess_specifyDate() {

        $this->target->setArgv([
            'date' => '2015-01-01'
        ]);
        $this->target->executeProcess();
        $this->expectOutputString("2015-01-01");
    }

    public function testExecuteProcess_specifyRange() {

        $this->target->setArgv([
            'since' => '2015-01-01',
            'until' => '2015-01-02'
        ]);
        $this->target->executeProcess();
        $this->expectOutputString("2015-01-012015-01-02");
    }
}

class DailyBatchRunnerTest_Dummy {

    public function doProcess($date) {
        echo $date;
    }
}