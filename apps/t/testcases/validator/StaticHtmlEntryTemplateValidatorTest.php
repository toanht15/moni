<?php

require_once dirname(__FILE__) . '/../../../config/define.php';
AAFW::import('jp.aainc.t.testcases.BaseTest');
AAFW::import('jp.aainc.lib.base.aafwValidatorBase');
AAFW::import('jp.aainc.aafw.classes.validator.StaticHtmlEntryTemplateValidator');

class StaticHtmlEntryTemplateValidatorTest extends BaseTest {
    private $validator;

    protected function setUp() {
        $this->validator = new StaticHtmlEntryTemplateValidator();
    }

    /**
     * @test
     * JSONが壊れている時
     */
    public function testIsValid_BrokenJson() {
        $json = "[{'a':'b'}]";
        $this->assertEquals(false, $this->validator->isValid($json));
    }

    /**
     * @test
     * テンプレート空っぽ
     */
    public function testIsValid_NotTemplate() {
        $json = "[]";
        $this->assertEquals(false, $this->validator->isValid($json));
    }

    /**
     * @test
     * image slider
     * view 色々空
     */
    public function testImageSlider_NotViewType() {
        $mock = new stdClass;
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
    }

    /**
     * @test
     * image slider
     * PCのカウント空
     */
    public function testImageSlider_NGPCCount() {
        $mock = new stdClass;
        $mock->slider_sp_image_count = 1;
        $mock->slider_pc_image_count = "a";
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
        $mock->slider_pc_image_count = "0";
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
        $mock->slider_pc_image_count = 0;
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
        $mock->slider_pc_image_count = "11";
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
        $mock->slider_pc_image_count = 11;
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
    }

    /**
     * @test
     * image slider
     * SPのカウント空
     */
    public function testImageSlider_NGSPCount() {
        $mock = new stdClass;
        $item = new stdClass;
        $item->image_url = 'http://test.com/test.jpg';
        $item->caption = 'test';
        $item->link = 'http://www.yahoo.co.jp/';
        $mock->item_list = array($item);

        $mock->slider_pc_image_count = 10;
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
        $mock->slider_sp_image_count = "0";
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
        $mock->slider_sp_image_count = 0;
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
        $mock->slider_sp_image_count = "11";
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
        $mock->slider_sp_image_count = 11;
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
    }

    /**
     * @test
     * image slider
     * 正常
     */
    public function testImageSlider_OKCount() {

        $mock = new stdClass;
        $item = new stdClass;
        $item->image_url = 'http://test.com/test.jpg';
        $item->caption = 'test';
        $item->link = 'http://www.yahoo.co.jp/';
        $mock->item_list = array($item);

        $mock->slider_pc_image_count = "10";
        $mock->slider_sp_image_count = "10";
        $this->assertEquals(true, $this->validator->isValidImageSlider($mock));
        $mock->slider_pc_image_count = "1";
        $mock->slider_sp_image_count = "1";
        $this->assertEquals(true, $this->validator->isValidImageSlider($mock));
        $mock->slider_pc_image_count = "5";
        $mock->slider_sp_image_count = "5";
        $this->assertEquals(true, $this->validator->isValidImageSlider($mock));
        $mock->slider_pc_image_count = "5";
        $mock->slider_sp_image_count = "5";
        $this->assertEquals(true, $this->validator->isValidImageSlider($mock));
    }

    /**
     * @test
     * image slider
     * 画像リスト内の画像URLが空
     */
    public function testImageSlider_NGItemImageUrl() {
        $item = new stdClass;
        $item->image_url = '';
        $item->caption = 'test';
        $item->link = 'http://www.yahoo.co.jp/';

        $mock = new stdClass;
        $mock->slider_pc_image_count = 5;
        $mock->slider_sp_image_count = 3;
        $mock->item_list = array($item);
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
    }

    /**
     * @test
     * image slider
     * 画像リスト内のキャプションが文字数オーバー
     */
    public function testImageSlider_NGItemCaption() {
        $item = new stdClass;
        $item->image_url = 'http://test.com/test.jpg';
        for($i = 0 ; $i < 21 ; $i++){
            $item->caption .= 'a';
        }
        $item->link = 'http://www.yahoo.co.jp/';

        $mock = new stdClass;
        $mock->slider_pc_image_count = 5;
        $mock->slider_sp_image_count = 3;
        $mock->item_list = array($item);
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
    }

    /**
     * @test
     * image slider
     * 画像リスト内のキャプションが文字数制限内
     */
    public function testImageSlider_OKItemCaption() {
        $item = new stdClass;
        $item->image_url = 'http://test.com/test.jpg';
        for($i = 0 ; $i < 20 ; $i++){
            $item->caption .= 'a';
        }
        $item->link = 'http://www.yahoo.co.jp/';

        $mock = new stdClass;
        $mock->slider_pc_image_count = 5;
        $mock->slider_sp_image_count = 3;
        $mock->item_list = array($item);
        $this->assertEquals(true, $this->validator->isValidImageSlider($mock));
    }

    /**
     * @test
     * image slider
     * 画像リスト内のリンクが非URL形式
     */
    public function testImageSlider_NGItemLink() {
        $item = new stdClass;
        $item->image_url = 'http://test.com/test.jpg';
        for($i = 0 ; $i < 20 ; $i++){
            $item->caption .= 'a';
        }
        $item->link = 'test';

        $mock = new stdClass;
        $mock->slider_pc_image_count = 5;
        $mock->slider_sp_image_count = 3;
        $mock->item_list = array($item);
        $this->assertEquals(false, $this->validator->isValidImageSlider($mock));
    }

    /**
     * @test
     * image slider
     * 画像リスト内のリンクがURL形式
     */
    public function testImageSlider_OKItemLink() {
        $item = new stdClass;
        $item->image_url = 'http://test.com/test.jpg';
        for($i = 0 ; $i < 20 ; $i++){
            $item->caption .= 'a';
        }
        $item->link = '';

        $mock = new stdClass;
        $mock->slider_pc_image_count = 5;
        $mock->slider_sp_image_count = 3;
        $mock->item_list = array($item);
        $this->assertEquals(true, $this->validator->isValidImageSlider($mock));
    }

    /**
     * @test
     * float image
     * 画像URLが空
     */
    public function testFloatImage_NotImage() {
        $mock = new stdClass;
        $mock->image_url = "";
        $mock->position_type = "1";
        $this->assertEquals(false, $this->validator->isValidFloatImage($mock));
    }

    /**
     * @test
     * float image
     * 画像URLが正しく設定
     */
    public function testFloatImage_ExistsImage() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->position_type = "1";
        $this->assertEquals(true, $this->validator->isValidFloatImage($mock));
    }

    /**
     * @test
     * float image
     * 位置設定が空
     */
    public function testFloatImage_NGPositionType() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->link = "test";
        $mock->position_type = "0";
        $this->assertEquals(false, $this->validator->isValidFloatImage($mock));
    }

    /**
     * @test
     * float image
     * 位置設定が異常値
     */
    public function testFloatImage_NGPositionType2() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->link = "test";
        $mock->position_type = "3";
        $this->assertEquals(false, $this->validator->isValidFloatImage($mock));
    }

    /**
     * @test
     * float image
     * 位置設定が正しい
     */
    public function testFloatImage_OKPositionType() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->link = "http://www.yahoo.co.jp";
        $mock->position_type = "1";
        $this->assertEquals(true, $this->validator->isValidFloatImage($mock));
    }

    /**
     * @test
     * float image
     * リンクが異常値
     */
    public function testFloatImage_NGLink() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->position_type = "1";
        $mock->link = "asdf";
        $this->assertEquals(false, $this->validator->isValidFloatImage($mock));
    }

    /**
     * @test
     * float image
     * リンクがOK
     */
    public function testFloatImage_OKLink() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->link = "http://www.yahoo.co.jp";
        $mock->position_type = "1";
        $this->assertEquals(true, $this->validator->isValidFloatImage($mock));
    }

    /**
     * @test
     * float image
     * キャプション長過ぎる
     */
    public function testFloatImage_TooLongCaption() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->link = "http://www.yahoo.co.jp";
        for($i = 0 ; $i < 21 ; $i++){
            $mock->caption .= 'a';
        }
        $mock->position_type = "1";
        $this->assertEquals(false, $this->validator->isValidFloatImage($mock));
    }

    /**
     * @test
     * float image
     * キャプションOK
     */
    public function testFloatImage_OKCaption() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->link = "http://www.yahoo.co.jp";
        for($i = 0 ; $i < 20 ; $i++){
            $mock->caption .= 'a';
        }
        $mock->position_type = "1";
        $this->assertEquals(true, $this->validator->isValidFloatImage($mock));
    }

    /**
     * @test
     * float image
     * テキスト長過ぎる
     */
    public function testFloatImage_TooLongText() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->link = "http://www.yahoo.co.jp";
        for($i = 0 ; $i < 20001 ; $i++){
            $mock->text .= 'a';
        }
        $mock->position_type = "1";
        $this->assertEquals(false, $this->validator->isValidFloatImage($mock));
    }

    /**
     * @test
     * float image
     * テキストOK
     */
    public function testFloatImage_OKText() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->link = "http://www.yahoo.co.jp";
        for($i = 0 ; $i < 20000 ; $i++){
            $mock->text .= 'a';
        }
        $mock->position_type = "1";
        $this->assertEquals(true, $this->validator->isValidFloatImage($mock));
    }

    /**
     * @test
     * full image
     * 画像なし
     */
    public function testFullImage_NotImage() {
        $mock = new stdClass;
        $mock->image_url = "";
        $this->assertEquals(false, $this->validator->isValidFullImage($mock));
    }

    /**
     * @test
     * full image
     * 画像あり
     */
    public function testFullImage_ExistsImage() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $this->assertEquals(true, $this->validator->isValidFullImage($mock));
    }

    /**
     * @test
     * full image
     * リンク異常値
     */
    public function testFullImage_NGLink() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->link = "test";
        $this->assertEquals(false, $this->validator->isValidFullImage($mock));
    }

    /**
     * @test
     * full image
     * リンクOK
     */
    public function testFullImage_OKLink() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->link = "http://www.yahoo.co.jp";
        $this->assertEquals(true, $this->validator->isValidFullImage($mock));
    }

    /**
     * @test
     * full image
     * キャプション長すぎ
     */
    public function testFullImage_TooLongCaption() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->link = "http://www.yahoo.co.jp";
        for($i = 0 ; $i < 21 ; $i++){
            $mock->caption .= 'a';
        }
        $this->assertEquals(false, $this->validator->isValidFullImage($mock));
    }

    /**
     * @test
     * full image
     * キャプションOK
     */
    public function testFullImage_OKCaption() {
        $mock = new stdClass;
        $mock->image_url = "http://test.com/test.jpg";
        $mock->link = "http://www.yahoo.co.jp";
        for($i = 0 ; $i < 19 ; $i++){
            $mock->caption .= 'a';
        }
        $this->assertEquals(true, $this->validator->isValidFullImage($mock));
    }

    /**
     * @test
     * text
     * テキスト無し
     */
    public function testText_NotText() {
        $mock = new stdClass;
        $mock->text = "";
        $this->assertEquals(false, $this->validator->isValidText($mock));
    }

    /**
     * @test
     * text
     * テキスト長すぎ
     */
    public function testText_TooLongText() {
        $mock = new stdClass;
        for($i = 0 ; $i < 20001 ; $i++){
            $mock->text .= 'a';
        }
        $this->assertEquals(false, $this->validator->isValidText($mock));
    }

    /**
     * @test
     * text
     * テキストあり。正常時
     */
    public function testText_Normal() {
        $mock = new stdClass;
        for($i = 0 ; $i < 20000 ; $i++){
            $mock->text .= 'a';
        }
        $this->assertEquals(true, $this->validator->isValidText($mock));
    }

}
