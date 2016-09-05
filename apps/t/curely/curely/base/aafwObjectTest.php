<?php
require_once __DIR__ . '/../../../../config/define.php';
AAFW::import ( 'jp.aainc.lib.base.aafwObject' );

class aafwObjectTest extends PHPUnit_Framework_TestCase {

    /** @var aafwObject $target */
	private $target = null;

	public function setUp(){
		$this->target = new aafwObject();
	}

//	/** @test */
//	public function 文字が指定文字長かどうか判断する_文字が指定文字列長の場合はtrueが返る(){
//		$result = $this->target->isStrLen(str_repeat('a',10),10);
//		$this->assertTrue($result == true );
//	}
//
//	/** @test */
//	public function 文字が指定文字長かどうか判断する_文字が指定文字列長以下の場合はfalseが返る(){
//		$result = $this->target->isStrLen(str_repeat('a',9),10);
//		$this->assertFalse($result);
//	}
//
//	/** @test */
//	public function 文字が指定文字長かどうか判断する_文字が指定文字列長以上の場合はfalseが返る(){
//		$result = $this->target->isStrLen(str_repeat('a',11),10);
//		$this->assertTrue($result == false );
//	}

	/** @test */
	public function 日付が正しいかどうか判断_日付形式が正しい場合はtrueが返る(){
		$result = $this->target->isDate('2012年1月1日');
		$this->assertTrue($result == true);
	}

	/** @test */
	public function 日付が正しいかどうか判断_日付形式が不正な場合はfalseが返る2(){
		$result = $this->target->isDate('2012年123月1日');
		$this->assertTrue($result == false);
	}

	/** @test */
	public function 日付が正しいかどうか判断_日付形式が不正な場合はfalseが返る3(){
		$result = $this->target->isDate('2012年123');
		$this->assertTrue($result == false);
	}

	/** @test */
	public function 日付が正しいかどうか判断_日付形式が不正な場合はfalseが返る4(){
		$result = $this->target->isDate('2012123');
		$this->assertTrue($result == false);
	}

	/** @test */
	public function 数値かどうか判断する_数値である場合はtrueが返る(){
		$result = $this->target->isNumeric(1);
		$this->assertTrue($result == true);
	}

	/** @test */
	public function 数値かどうか判断する_数値ではない場合はfalseが返る(){
		$result = $this->target->isNumeric('abc');
		$this->assertTrue($result == false);
	}

	/** @test */
	public function メールアドレスのフォーマットが正しければtrue() {
		$str = 'aaaa@disney.ne.jp';
		$flg = $this->target->isMailAddress($str);
		$this->assertTrue($flg == true);
	}

    public function test_isAlnumSymbol_かな() {
        $str = 'あ';
        $this->assertFalse($this->target->isAlnumSymbol($str));
    }

    public function test_isAlnumSymbol_カナ() {
        $str = 'ア';
        $this->assertFalse($this->target->isAlnumSymbol($str));
    }

    public function test_isAlnumSymbol_英語() {
        $str = 'abcd';
        $this->assertTrue($this->target->isAlnumSymbol($str));
    }

    public function test_isAlnumSymbol_記号() {
        $str = '!@#$%^&';
        $this->assertTrue($this->target->isAlnumSymbol($str));
    }

    public function test_isAlnumSymbol_数字() {
        $str = '1234567';
        $this->assertTrue($this->target->isAlnumSymbol($str));
    }
}
