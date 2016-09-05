<?php
AAFW::import ('jp.aainc.classes.services.ManagerService');
AAFW::import('jp.aainc.lib.db.aafwRedisManager');
AAFW::import('jp.aainc.classes.util.TokenWithoutSimilarCharGenerator');
AAFW::import('jp.aainc.classes.CacheManager');

class ManagerServiceTest extends BaseTest {

    /** @var  ManagerService $target */
    private $target;
    private $token_key;

    public function setUp() {
        $this->target = aafwServiceFactory::create("ManagerService");
    }

    public function testGenerateOnetimeToken() {

        $return = $this->target->generateOnetimeToken('test');
        $result = explode('=', $return);

        $this->assertEquals(ManagerService::TOKEN_KEY, $result[0]);
    }

    public function testVerifyOnetimeToken_true() {

        $return = $this->target->generateOnetimeToken('test');
        $result = explode('=', $return);

        $return = $this->target->verifyOnetimeToken($result[1]);

        $this->assertEquals('test', $return);
    }

    public function testVerifyOnetimeToken_delete() {

        $return = $this->target->generateOnetimeToken('test');
        $result = explode('=', $return);

        $this->target->verifyOnetimeToken($result[1]);

        $return = $this->target->verifyOnetimeToken($result[1]);

        $this->assertEquals(False, $return);//トークンが消えていることを確認
    }

    public function testVerifyOnetimeToken_false() {

        $return = $this->target->verifyOnetimeToken('hoge');

        $this->assertEquals('', $return);
    }

}