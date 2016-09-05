<?php

AAFW::import('jp.aainc.classes.util.Hash');

/**
 * Class HashTest
 */
class HashTest extends PHPUnit_Framework_TestCase {
    /** @var Hash */
    private $gen;

    public function setUp() {
        $this->gen = new Hash();
    }

    /**
     * @test
     */
    public function デフォルトsh256で文字列が生成できること() {
        $data = 'data1';
        $salt = 'salt1';
        $hash = $this->gen->doHash($data, $salt);
        $this->assertSame(64, strlen($hash));
    }

    /**
     * @test
     */
    public function sha1で文字列が生成できること() {
        $data = 'data1';
        $salt = 'salt1';
        $loopCount = 1;
        $algo = 'sha1';
        $hash = $this->gen->doHash($data, $salt, $loopCount, $algo);
        $this->assertSame(40, strlen($hash));
    }

    /**
     * @test
     */
    public function 引数の例外テスト_data() {
        $data = '';
        $salt = 'salt1';
        try {
            $hash = $this->gen->doHash($data, $salt);
            $this->fail('期待したExceptionが検出できませんでした');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * @test
     */
    public function 引数の例外テスト_salt() {
        $data = 'data1';
        $salt = '';
        try {
            $hash = $this->gen->doHash($data, $salt);
            $this->fail('期待したExceptionが検出できませんでした');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * @test
     */
    public function 引数の例外テスト_loopCount() {
        $data = 'data1';
        $salt = 'salt1';
        $loopCount = 0;
        try {
            $hash = $this->gen->doHash($data, $salt, $loopCount);
            $this->fail('期待したExceptionが検出できませんでした');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * @test
     */
    public function 引数の例外テスト_algorithm() {
        $data = 'data1';
        $salt = 'salt1';
        $loopCount = 1;
        $algo = '';
        try {
            $hash = $this->gen->doHash($data, $salt, $loopCount, $algo);
            $this->fail('期待したExceptionが検出できませんでした');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * @test
     */
    public function 正常な動作_doHashMd5Email() {
        $this->assertEquals("4f64c9f81bb0d4ee969aaf7b4a5a6f40",$this->gen->doHashMd5Email("email@email.com"));
    }

    /**
     * @test
     */
    public function 空白が入っててもtrimされてハッシュ_doHashMd5Email() {
        $this->assertEquals("4f64c9f81bb0d4ee969aaf7b4a5a6f40",$this->gen->doHashMd5Email(" email@email.com "));
    }

    /**
     * @test
     */
    public function 大文字はlowerされてハッシュ_doHashMd5Email() {
        $this->assertEquals("4f64c9f81bb0d4ee969aaf7b4a5a6f40",$this->gen->doHashMd5Email("EMAIL@EMAIL.COM"));
    }

    /**
     * @test
     */
    public function メールアドレスの形式じゃなかったら空文字帰る_doHashMd5Email() {
        $result= $this->gen->doHashMd5Email("Not Email Address");
        $this->assertEquals("",$result);
    }


}
