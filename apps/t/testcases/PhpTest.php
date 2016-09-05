<?php

/**
 * Class HashTest
 */
class PhpTest extends PHPUnit_Framework_TestCase {

    public function test_特殊文字_半角スペース() {
        if (preg_match('#\~|\%|\:|\/|\?|\#|\[|\]|\@|\!|\$|\&|\(|\)|\*|\+|\,| |　#', ' ')) {
            $this->assertTrue(true);
        }
    }

    public function test_特殊文字_全角スペース() {
        if (preg_match('#\~|\%|\:|\/|\?|\#|\[|\]|\@|\!|\$|\&|\(|\)|\*|\+|\,| |　#', '　')) {
            $this->assertTrue(true);
        }
    }

    public function test_特殊文字_マッチしない() {
        if (!preg_match('#\~|\%|\:|\/|\?|\#|\[|\]|\@|\!|\$|\&|\(|\)|\*|\+|\,| |　#', 'A')) {
            $this->assertTrue(true);
        }
    }

    public function test_empty_0() {
        $val = 0;
        $result = empty($val);
        $this->assertEquals(true, $result);
    }

    public function test_empty_1() {
        $val = 1;
        $result = empty($val);
        $this->assertEquals(false, $result);
    }

    public function test_empty_2() {
        $val = '';
        $result = empty($val);
        $this->assertEquals(true, $result);
    }

    public function test_empty_3() {
        $val = null;
        $result = empty($val);
        $this->assertEquals(true, $result);
    }

    public function test_empty_4() {
        $val = '0';
        $result = empty($val);
        $this->assertEquals(true, $result);
    }

    public function test_empty_5() {
        $val = '1';
        $result = empty($val);
        $this->assertEquals(false, $result);
    }

    public function test_empty_6() {
        $val = 'A';
        $result = empty($val);
        $this->assertEquals(false, $result);
    }

    public function test_bool_0() {
        $val = 1;
        $result = ($val == 1);
        $this->assertEquals(true, $result);
    }

    public function test_bool_1() {
        $val = '1';
        $result = ($val == 1);
        $this->assertEquals(true, $result);
    }

    public function test_bool_2() {
        $val = '1';
        $result = ($val == 2);
        $this->assertEquals(false, $result);
    }

    public function test_bool_3() {
        $val = 1;
        $result = ($val == true);
        $this->assertEquals(true, $result);
    }

    public function test_bool_4() {
        $val = 1;
        $result = ($val == false);
        $this->assertEquals(false, $result);
    }

    public function test_bool_5() {
        $val = '1';
        $result = ($val == true);
        $this->assertEquals(true, $result);
    }

    public function test_bool_6() {
        $val = '1';
        $result = ($val == false);
        $this->assertEquals(false, $result);
    }

    public function test_bool_7() {
        $val = 1;
        $result = ($val === false);
        $this->assertEquals(false, $result);
    }

    public function test_bool_8() {
        $val = '1';
        $result = ($val === false);
        $this->assertEquals(false, $result);
    }

    public function test_bool_9() {
        $val = true;
        $result = ($val === true);
        $this->assertEquals(true, $result);
    }

    public function test_1週間前の日付を取得する1() {
        $date = date('Y-m-d H:i:s', strtotime('2016-05-31 -7 day'));
        $this->assertEquals('2016-05-24 00:00:00', $date);
    }

    public function test_1週間前の日付を取得する2() {
        $date = date('Y-m-d H:i:s', strtotime('2016-01-01 -7 day'));
        $this->assertEquals('2015-12-25 00:00:00', $date);
    }
}
