<?php
AAFW::import('jp.aainc.classes.services.instant_win.SynInstantWinService');

class SynInstantWinServiceTest extends BaseTest {

    /** @var  SynInstantWinService */
    private $target;

    protected function setUp() {
    }

    /**
     * @test
     */
    public function test_初挑戦の人はダブルアップではない() {
        $this->target = $this->getMock(
            'SynInstantWinService',
            array('findLastChallengeLog', 'getCurrentTimestamp', 'findDrawableSecondChallengeLog')
        );

        $this->target->expects($this->once())
            ->method('findLastChallengeLog')
            ->will($this->returnValue(null));

        $this->target->expects($this->once())
            ->method('findDrawableSecondChallengeLog')
            ->will($this->returnValue(null));
        $this->assertEquals(false, $this->target->isDoubleUpChallenge(1, 1));
    }

    /**
     * @test
     */
    public function test_ギリギリ連続で参加してる() {
        $this->target = $this->getMock(
            'SynInstantWinService',
            array('findLastChallengeLog', 'getCurrentTimestamp', 'findDrawableSecondChallengeLog')
        );

        $lastChallengeLog = new SynCpChallengeLog();
        $lastChallengeLog->challenged_at = '2016-01-01 12:00:00';
        $this->target->expects($this->once())
            ->method('findLastChallengeLog')
            ->will($this->returnValue($lastChallengeLog));

        $currentChallengeTimestamp = strtotime("2016-01-02 23:59:59");
        $this->target->expects($this->once())
            ->method('getCurrentTimestamp')
            ->will($this->returnValue($currentChallengeTimestamp));

        $this->target->expects($this->once())
            ->method('findDrawableSecondChallengeLog')
            ->will($this->returnValue(null));
        $this->assertEquals(true, $this->target->isDoubleUpChallenge(1, 0));
    }

    /**
     * @test
     */
    public function test_ギリギリ連続逃した() {
        $this->target = $this->getMock(
            'SynInstantWinService',
            array('findLastChallengeLog', 'getCurrentTimestamp', 'findDrawableSecondChallengeLog')
        );

        $lastChallengeLog = new SynCpChallengeLog();
        $lastChallengeLog->challenged_at = '2016-01-01 12:00:00';
        $this->target->expects($this->once())
            ->method('findLastChallengeLog')
            ->will($this->returnValue($lastChallengeLog));

        $currentChallengeTimestamp = strtotime("2016-01-03 00:00:00");
        $this->target->expects($this->once())
            ->method('getCurrentTimestamp')
            ->will($this->returnValue($currentChallengeTimestamp));

        $this->target->expects($this->once())
            ->method('findDrawableSecondChallengeLog')
            ->will($this->returnValue(null));

        $this->assertEquals(false, $this->target->isDoubleUpChallenge(1, 0));
    }

    /**
     * @test
     */
    public function test_連続チャレンジ中はchallenge_modeの値を見る_2倍() {
        $this->target = $this->getMock('SynInstantWinService', array('findDrawableSecondChallengeLog'));

        $continuityLog = new SynCpSecondChallengeLog();
        $continuityLog->challenge_mode = SynCpSecondChallengeLog::DOUBLE_UP_CHALLENGE;
        $this->target->expects($this->once())
            ->method('findDrawableSecondChallengeLog')
            ->will($this->returnValue($continuityLog));

        $this->assertEquals(true, $this->target->isDoubleUpChallenge(1, 1));
    }

    /**
     * @test
     */
    public function test_連続チャレンジ中はchallenge_modeの値を見る_1倍() {
        $this->target = $this->getMock('SynInstantWinService', array('findDrawableSecondChallengeLog'));

        $continuityLog = new SynCpSecondChallengeLog();
        $continuityLog->challenge_mode = SynCpSecondChallengeLog::NORMAL_CHALLENGE;
        $this->target->expects($this->once())
            ->method('findDrawableSecondChallengeLog')
            ->will($this->returnValue($continuityLog));

        $this->assertEquals(false, $this->target->isDoubleUpChallenge(1, 1));
    }

    /**
     * @test
     */
    public function test_drawFirstChallenge連続チャレンジの時はsyn_cp_continuity_challenge_logsのchallenge_modeが2で保存される() {
        $this->target = $this->getMock('SynInstantWinService', array('isDoubleUpChallenge', 'getModel'));
        $this->target->expects($this->once())
            ->method('isDoubleUpChallenge')
            ->will($this->returnValue(true));

        $mockStore = $this->getMock('aafwEntityStoreBase', array('createEmptyObject', 'save'));
        $hoge = new SynCpSecondChallengeLog();
        $mockStore->expects($this->any())
            ->method('createEmptyObject')
            ->will($this->returnValue(new SynCpChallengeLog()))
            ->will($this->returnValue($hoge));

        $this->target->expects($this->any())
            ->method('getModel')
            ->will($this->returnValue($mockStore));

        $this->target->drawFirstChallenge(1, 1);
        $this->assertTrue($hoge->challenge_mode == SynCpSecondChallengeLog::DOUBLE_UP_CHALLENGE);
    }

    /**
     * @test
     */
    public function test_drawFirstChallenge初回のチャレンジの時はsyn_cp_continuity_challenge_logsのchallenge_modeが1で保存される() {
        $this->target = $this->getMock('SynInstantWinService', array('isDoubleUpChallenge', 'getModel'));
        $this->target->expects($this->once())
            ->method('isDoubleUpChallenge')
            ->will($this->returnValue(false));

        $mockStore = $this->getMock('aafwEntityStoreBase', array('createEmptyObject', 'save'));
        $synCpContinuityChallengeLog = new SynCpSecondChallengeLog();
        $mockStore->expects($this->any())
            ->method('createEmptyObject')
            ->will($this->returnValue(new SynCpChallengeLog()))
            ->will($this->returnValue($synCpContinuityChallengeLog));

        $this->target->expects($this->any())
            ->method('getModel')
            ->will($this->returnValue($mockStore));

        $this->target->drawFirstChallenge(1, 1);
        $this->assertTrue(
            $synCpContinuityChallengeLog->challenge_mode == SynCpSecondChallengeLog::NORMAL_CHALLENGE
        );
    }

    /**
     * @test
     */
    public function test_getTodayBetweenChallengedAtリセット時間0000() {
        $this->target = $this->getMock('SynInstantWinService', array('getResetDate','isOverResetTime'));

        $this->target->expects($this->any())
            ->method('getResetDate')
            ->will($this->returnValue("2016-12-12 00:00:00"));
        $this->target->expects($this->any())
            ->method('isOverResetTime')
            ->will($this->returnValue(true));

        list($beginChallengedAt, $endChallengedAt) = $this->target->getTodayBetweenChallengedAt();
        $this->assertEquals("2016-12-12 00:00:00", $beginChallengedAt);
        $this->assertEquals("2016-12-13 00:00:00", $endChallengedAt);
    }

    /**
     * @test
     */
    public function test_getTodayBetweenChallengedAtリセット時間1200で今日リセット時間を迎えている() {
        $this->target = $this->getMock('SynInstantWinService', array('getResetDate','isOverResetTime'));

        $this->target->expects($this->any())
            ->method('getResetDate')
            ->will($this->returnValue("2016-12-12 12:00:00"));
        $this->target->expects($this->any())
            ->method('isOverResetTime')
            ->will($this->returnValue(true));

        list($beginChallengedAt, $endChallengedAt) = $this->target->getTodayBetweenChallengedAt();
        $this->assertEquals("2016-12-12 12:00:00", $beginChallengedAt);
        $this->assertEquals("2016-12-13 12:00:00", $endChallengedAt);
    }

    /**
     * @test
     */
    public function test_getTodayBetweenChallengedAtリセット時間1200で今日まだリセット時間になっていない() {
        $this->target = $this->getMock('SynInstantWinService', array('getResetDate','isOverResetTime'));

        $this->target->expects($this->any())
            ->method('getResetDate')
            ->will($this->returnValue("2016-12-12 12:00:00"));
        $this->target->expects($this->any())
            ->method('isOverResetTime')
            ->will($this->returnValue(false));

        list($beginChallengedAt, $endChallengedAt) = $this->target->getTodayBetweenChallengedAt();
        $this->assertEquals("2016-12-11 12:00:00", $beginChallengedAt);
        $this->assertEquals("2016-12-12 12:00:00", $endChallengedAt);
    }

}