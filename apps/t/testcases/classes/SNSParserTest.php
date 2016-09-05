<?php

class SNSParserTest extends BaseTest {

    /**
     * @test
     */
    public function parseTextTW_Test() {
        $panel_text = 'testtesttesttesttesttesttest #test';
        $result = SNSParser::parseText($panel_text, SNSParser::TWITTER_PANEL_TEXT);
        $this->assertThat($result, $this->equalTo('testtesttesttesttesttesttest <a href="https://twitter.com/hashtag/test?src=hash" target="_blank">#test</a>'));
    }

    /**
     * @test
     */
    public function parseTextTW_Test2() {
        $panel_text = 'testtesttesttesttesttesttest #test #abcdefあいうえお';
        $result = SNSParser::parseText($panel_text, SNSParser::TWITTER_PANEL_TEXT);
        $this->assertThat($result, $this->equalTo('testtesttesttesttesttesttest <a href="https://twitter.com/hashtag/test?src=hash" target="_blank">#test</a> <a href="https://twitter.com/hashtag/abcdefあいうえお?src=hash" target="_blank">#abcdefあいうえお</a>'));
    }

    /**
     * @test
     */
    public function parseTextTW_Test3() {
        $panel_text = 'testtesttesttesttesttesttest #test #abcdefあいうえお @taisa_007';
        $result = SNSParser::parseText($panel_text, SNSParser::TWITTER_PANEL_TEXT);
        $this->assertThat($result, $this->equalTo('testtesttesttesttesttesttest <a href="https://twitter.com/hashtag/test?src=hash" target="_blank">#test</a> <a href="https://twitter.com/hashtag/abcdefあいうえお?src=hash" target="_blank">#abcdefあいうえお</a> <a href="https://twitter.com/taisa_007" target="_blank">@taisa_007</a>'));
    }

    /**
     * @test
     */
    public function parseTextTW_Test4() {
        $panel_text = 'testtesttesttesttesttesttest #test #abcdefあいうえお @taisa_007 http://yahoo.co.jp';
        $result = SNSParser::parseText($panel_text, SNSParser::TWITTER_PANEL_TEXT);
        $this->assertThat($result, $this->equalTo('testtesttesttesttesttesttest <a href="https://twitter.com/hashtag/test?src=hash" target="_blank">#test</a> <a href="https://twitter.com/hashtag/abcdefあいうえお?src=hash" target="_blank">#abcdefあいうえお</a> <a href="https://twitter.com/taisa_007" target="_blank">@taisa_007</a> <a href="http://yahoo.co.jp" target="_blank">http://yahoo.co.jp</a>'));
    }

    /**
     * @test
     */
    public function parseTextFB_Test() {
        $panel_text = 'testtesttesttesttesttesttest #test';
        $result = SNSParser::parseText($panel_text, SNSParser::FACEBOOK_PANEL_TEXT);
        $this->assertThat($result, $this->equalTo('testtesttesttesttesttesttest <a href="https://www.facebook.com/hashtag/test" target="_blank">#test</a>'));
    }

    /**
     * @test
     */
    public function parseTextFB_Test2() {
        $panel_text = 'testtesttesttesttesttesttest #test #abcdefあいうえお';
        $result = SNSParser::parseText($panel_text, SNSParser::FACEBOOK_PANEL_TEXT);
        $this->assertThat($result, $this->equalTo('testtesttesttesttesttesttest <a href="https://www.facebook.com/hashtag/test" target="_blank">#test</a> <a href="https://www.facebook.com/hashtag/abcdefあいうえお" target="_blank">#abcdefあいうえお</a>'));
    }

    /**
     * @test
     */
    public function parseTextFB_Test3() {
        $panel_text = 'testtesttesttesttesttesttest #test #abcdefあいうえお @taisa_007';
        $result = SNSParser::parseText($panel_text, SNSParser::FACEBOOK_PANEL_TEXT);
        $this->assertThat($result, $this->equalTo('testtesttesttesttesttesttest <a href="https://www.facebook.com/hashtag/test" target="_blank">#test</a> <a href="https://www.facebook.com/hashtag/abcdefあいうえお" target="_blank">#abcdefあいうえお</a> @taisa_007'));
    }

    /**
     * Informal Mention test
     * @test
     */
    public function parseInformalTWTest01() {
        $panel_text = '@@just4fun1503 @just4fun1503 ＠just4fun1503 @just4fun1503@ t@just4fun1503 t-@just4fun1503 @just4fun1503_123 @just4fun1503-t @テスト';
        $result = SNSParser::parseText($panel_text, SNSParser::TWITTER_PANEL_TEXT);
        $this->assertThat($result, $this->equalTo('@<a href="https://twitter.com/just4fun1503" target="_blank">@just4fun1503</a> <a href="https://twitter.com/just4fun1503" target="_blank">@just4fun1503</a> <a href="https://twitter.com/just4fun1503" target="_blank">＠just4fun1503</a> @just4fun1503@ t@just4fun1503 t-<a href="https://twitter.com/just4fun1503" target="_blank">@just4fun1503</a> <a href="https://twitter.com/just4fun1503_123" target="_blank">@just4fun1503_123</a> <a href="https://twitter.com/just4fun1503" target="_blank">@just4fun1503</a>-t @テスト'));
    }

    /**
     * Informal Hashtag test
     * @test
     */
    public function parseInformalTWTest02() {
        $panel_text = '#test #テスト ##test a#test #大航海時代OnlineをするならGTune ＃勉強テスト勇気SUPPERSTAR a-#test #test# →#test /#test ?#test #test/ #test_test@ #http://google.com #http:/ #http:// #123 #123asdf #テスト１２３ テス@testさん #船 #まらない #狂人 #麤 #纊 #ⅹ #黑';
        $result = SNSParser::parseText($panel_text, SNSParser::TWITTER_PANEL_TEXT);
        $this->assertThat($result, $this->equalTo('<a href="https://twitter.com/hashtag/test?src=hash" target="_blank">#test</a> <a href="https://twitter.com/hashtag/テスト?src=hash" target="_blank">#テスト</a> #<a href="https://twitter.com/hashtag/test?src=hash" target="_blank">#test</a> a#test <a href="https://twitter.com/hashtag/大航海時代OnlineをするならGTune?src=hash" target="_blank">#大航海時代OnlineをするならGTune</a> <a href="https://twitter.com/hashtag/勉強テスト勇気SUPPERSTAR?src=hash" target="_blank">＃勉強テスト勇気SUPPERSTAR</a> a-<a href="https://twitter.com/hashtag/test?src=hash" target="_blank">#test</a> #test# →<a href="https://twitter.com/hashtag/test?src=hash" target="_blank">#test</a> /#test ?#test <a href="https://twitter.com/hashtag/test?src=hash" target="_blank">#test</a>/ <a href="https://twitter.com/hashtag/test_test?src=hash" target="_blank">#test_test</a>@ #http://google.com <a href="https://twitter.com/hashtag/http?src=hash" target="_blank">#http</a>:/ #http:// #123 <a href="https://twitter.com/hashtag/123asdf?src=hash" target="_blank">#123asdf</a> <a href="https://twitter.com/hashtag/テスト１２３?src=hash" target="_blank">#テスト１２３</a> テス<a href="https://twitter.com/test" target="_blank">@test</a>さん <a href="https://twitter.com/hashtag/船?src=hash" target="_blank">#船</a> <a href="https://twitter.com/hashtag/まらない?src=hash" target="_blank">#まらない</a> <a href="https://twitter.com/hashtag/狂人?src=hash" target="_blank">#狂人</a> <a href="https://twitter.com/hashtag/麤?src=hash" target="_blank">#麤</a> <a href="https://twitter.com/hashtag/纊?src=hash" target="_blank">#纊</a> #ⅹ <a href="https://twitter.com/hashtag/黑?src=hash" target="_blank">#黑</a>'));
    }

    /**
     * Informal FB Hashtag test
     * @test
     */
    public function parseInformalFBTest01() {
        $panel_text = '#test #テスト ##test a#test #大航海時代OnlineをするならGTune ＃勉強テスト勇気SUPPERSTAR a-#test #test# →#test /#test ?#test #test/ #test_test@ #http://google.com #http:/ #http:// #123 #123asdf #テスト１２３ テス@testさん #船 #まらない #狂人 #麤 #纊 #ⅹ #黑';
        $result = SNSParser::parseText($panel_text, SNSParser::FACEBOOK_PANEL_TEXT);
        $this->assertThat($result, $this->equalTo('<a href="https://www.facebook.com/hashtag/test" target="_blank">#test</a> <a href="https://www.facebook.com/hashtag/テスト" target="_blank">#テスト</a> #<a href="https://www.facebook.com/hashtag/test" target="_blank">#test</a> a#test <a href="https://www.facebook.com/hashtag/大航海時代OnlineをするならGTune" target="_blank">#大航海時代OnlineをするならGTune</a> <a href="https://www.facebook.com/hashtag/勉強テスト勇気SUPPERSTAR" target="_blank">＃勉強テスト勇気SUPPERSTAR</a> a-<a href="https://www.facebook.com/hashtag/test" target="_blank">#test</a> <a href="https://www.facebook.com/hashtag/test" target="_blank">#test</a># →<a href="https://www.facebook.com/hashtag/test" target="_blank">#test</a> /#test ?#test <a href="https://www.facebook.com/hashtag/test" target="_blank">#test</a>/ <a href="https://www.facebook.com/hashtag/test_test" target="_blank">#test_test</a>@ #http://google.com <a href="https://www.facebook.com/hashtag/http" target="_blank">#http</a>:/ #http:// #123 <a href="https://www.facebook.com/hashtag/123asdf" target="_blank">#123asdf</a> <a href="https://www.facebook.com/hashtag/テスト１２３" target="_blank">#テスト１２３</a> テス@testさん <a href="https://www.facebook.com/hashtag/船" target="_blank">#船</a> <a href="https://www.facebook.com/hashtag/まらない" target="_blank">#まらない</a> <a href="https://www.facebook.com/hashtag/狂人" target="_blank">#狂人</a> <a href="https://www.facebook.com/hashtag/麤" target="_blank">#麤</a> <a href="https://www.facebook.com/hashtag/纊" target="_blank">#纊</a> #ⅹ <a href="https://www.facebook.com/hashtag/黑" target="_blank">#黑</a>'));
    }

    /**
     * Informal FB Link test
     * @test
     */
    public function parseInformalUrlTest01() {
        $panel_test = 'http://google.com http://facebook.com... (http://google.com) →http://google.com asdfhttp://google.com "http://google.com" a_http://google.com ://google.com google.com /test';
        $result = SNSParser::parseText($panel_test, SNSParser::FACEBOOK_PANEL_TEXT);
        $this->assertThat($result, $this->equalTo('<a href="http://google.com" target="_blank">http://google.com</a> http://facebook.com... (<a href="http://google.com" target="_blank">http://google.com</a>) →<a href="http://google.com" target="_blank">http://google.com</a> asdfhttp://google.com "<a href="http://google.com" target="_blank">http://google.com</a>" a_<a href="http://google.com" target="_blank">http://google.com</a> ://google.com google.com /test'));
    }
}
