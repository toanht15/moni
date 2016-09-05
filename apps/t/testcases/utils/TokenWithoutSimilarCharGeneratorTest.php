<?php

AAFW::import('jp.aainc.classes.util.TokenWithoutSimilarCharGenerator');

/**
 * Class TokenWithoutSimilarCharGeneratorTest
 */
class TokenWithoutSimilarCharGeneratorTest extends PHPUnit_Framework_TestCase {
    /** @var  TokenWithoutSimilarCharGenerator */
    private $gen;

    public function setUp() {
        $this->gen = new TokenWithoutSimilarCharGenerator();
    }

    /**
     * @test
     */
    public function 指定文字数の文字列が生成できること() {
        $token = $this->gen->generateToken(123);
        $this->assertSame(123, strlen($token));
    }

    /**
     * @test
     * @dataProvider 類似文字リスト
     */
    public function 類似文字の入っていない文字列が生成できること($val) {
        $token = $this->gen->generateToken(512);
        $this->assertFalse(strpos($token, $val));
    }

    /**
     * @return array
     */
    public function 類似文字リスト() {
        return [
            ['i'],
            ['j'],
            ['l'],
            ['I'],
            ['J'],
            ['o'],
            ['O'],
            ['Z'],
            ['0'],
            ['1'],
            ['2'],
        ];
    }
}
