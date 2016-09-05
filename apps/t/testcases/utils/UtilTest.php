<?php

AAFW::import('jp.aainc.aafw.classes.Util');
AAFW::import('jp.aainc.aafw.classes.entities.Brand');

class UtilTest extends BaseTest {

    public function testGetShortenUrlByBitly() {
        $this->assertNotNull(Util::getShortenUrlByBitly('http://allied-id.com'));
    }

    public function testTrimEmSpace_whenSpacesExist() {
        $this->assertEquals('TEST 　TEST', Util::trimEmSpace(' 　TEST 　TEST 　'));
    }

    public function testTrimEmSpace_whenSpacesDoNotExist() {
        $this->assertEquals('TEST 　TEST', Util::trimEmSpace('TEST 　TEST'));
    }

    public function testGetClientIP_when_HTTP_X_FORWARDED_FORexists() {
        putenv('HTTP_X_FORWARDED_FOR=TEST1');
        $this->assertEquals('TEST1', Util::getClientIP());
    }

    public function testGetClientIP_when_REMOTE_ADDR_exists() {
        putenv('HTTP_X_FORWARDED_FOR=');
        putenv('REMOTE_ADDR=TEST2');
        $this->assertEquals('TEST2', Util::getClientIP());
    }

    public function testGetClientIP_when_HTTP_CLIENT_IP_exists() {
        putenv('HTTP_X_FORWARDED_FOR=');
        putenv('REMOTE_ADDR=');
        putenv('HTTP_CLIENT_IP=TEST3');
        $this->assertEquals('TEST3', Util::getClientIP());
    }

    public function testGetClientIP_when_noneOfEnv_exists() {
        putenv('HTTP_X_FORWARDED_FOR=');
        putenv('REMOTE_ADDR=');
        putenv('HTTP_CLIENT_IP=');
        $this->assertEquals(null, Util::getClientIP());
    }

    public function testCreateBaseUrl_whenNotSecureAndNotMapped() {
        $brand = new Brand();
        $brand->id = 1;
        $brand->directory_name = 'directory';
        $this->assertEquals('http://brandcotest.com/directory/', Util::createBaseUrl($brand, false));
    }

    public function testCreateBaseUrl_whenSecureAndMapped() {
        $brand = new Brand();
        $brand->id = 99;
        $brand->directory_name = 'test.com';
        $this->assertEquals('https://test.com/', Util::createBaseUrl($brand, true));
    }

    public function testRewriteUrl_whenAllOfParametersExist() {
        $rewritted_url = Util::rewriteUrl('package', 'action',
            array('p1' => 'param1', 'p2' => 'param2'), array('qp1' => 'val1', 'qp2' => 'val2'),
            'http://brandcotest.com/directory/');
        $this->assertEquals('http://brandcotest.com/directory/package/action/param1/param2?qp1=val1&qp2=val2',
            $rewritted_url);
    }

    public function testRewriteUrl_when_actionExists_and_arrayParamIsEmpty_and_queryParamIsEmpty_and_baseUrlIsEmpty_and_secure() {
        $_SERVER['REQUEST_URI'] = '/directory/hogehoge';
        $this->assertEquals('https://brandcotest.com/directory/action',
            Util::rewriteUrl('', 'action', array(), array(), null, true));
    }

    public function testRewriteUrl_whenAllOfParametersAreEmpty() {
        $_SERVER['REQUEST_URI'] = '/directory/hogehoge';
        $this->assertEquals('http://brandcotest.com/directory/', Util::rewriteUrl('', '', array(), array(), null));
    }

    public function testParseRequestUri_whenActionIsTheRoot() {
        $this->assertEquals(json_encode(array('action' => 'index')), json_encode(Util::parseRequestUri('/')));
    }

    public function testParseRequestUri_whenDirectoryOnly() {
        $this->newController('user');
        $this->assertEquals(json_encode(
            array('__path' => array('directory'), 'directory_name' => 'directory', 'req' => null, 'exts' => array(), 'action' => 'index', 'package' => 'brandco/')),
            json_encode(Util::parseRequestUri('/directory')));
    }

    public function testParseRequestUri_whenActionOnly() {
        $this->newController('user');
        $this->assertEquals(json_encode(
            array('__path' => array('directory', 'campaigns'), 'directory_name' => 'directory', 'req' => null, 'exts' => array(), 'action' => 'campaigns', 'package' => 'brandco/')),
            json_encode(Util::parseRequestUri('/directory/campaigns')));
    }

    public function testIsManagerIp_networkAddress() {
        putenv('HTTP_X_FORWARDED_FOR=124.33.221.200');
        $this->assertEquals(Util::isManagerIp(), false);
    }

    public function testIsManagerIp_01() {
        putenv('HTTP_X_FORWARDED_FOR=124.33.221.201');
        $this->assertEquals(Util::isManagerIp(), true);
    }

    public function testIsManagerIp_02() {
        putenv('HTTP_X_FORWARDED_FOR=124.33.221.206');
        $this->assertEquals(Util::isManagerIp(), true);
    }

    public function testIsManagerIp_broadcast() {
        putenv('HTTP_X_FORWARDED_FOR=124.33.221.207');
        $this->assertEquals(Util::isManagerIp(), false);
    }

    public function testIsManagerIp_privateMachine() {
        putenv('HTTP_X_FORWARDED_FOR=124.35.221.2');
        $this->assertEquals(Util::isManagerIp('124.35.221.2'), true);
    }

    public function testIsManagerIp_現在のIP() {
        putenv('HTTP_X_FORWARDED_FOR=210.138.60.124');
        $this->assertEquals(Util::isManagerIp('210.138.60.124'), true);
    }

    public function testIsManagerIp_null() {
        putenv('HTTP_X_FORWARDED_FOR=');
        $this->assertEquals(Util::isManagerIp(), false);
    }

    public function testIsManagerIp_空() {
        putenv('HTTP_X_FORWARDED_FOR=   ');
        $this->assertEquals(Util::isManagerIp(''), false);
    }

    public function testIsManagerIp_任意() {
        putenv('HTTP_X_FORWARDED_FOR=テキストIP');
        $this->assertEquals(Util::isManagerIp(), false);
    }

    public function testIsAcceptRemote_最小IP() {
        putenv('HTTP_X_FORWARDED_FOR=124.33.221.200');
        $this->assertEquals(Util::isAcceptRemote(), true);
    }

    public function testIsAcceptRemote_01() {
        putenv('HTTP_X_FORWARDED_FOR=124.33.221.201');
        $this->assertEquals(Util::isAcceptRemote(), false);
    }

    public function testIsAcceptRemote_06() {
        putenv('HTTP_X_FORWARDED_FOR=124.33.221.206');
        $this->assertEquals(Util::isAcceptRemote(), false);
    }

    public function testIsAcceptRemote_broadcast() {
        putenv('HTTP_X_FORWARDED_FOR=124.33.221.207');
        $this->assertEquals(Util::isAcceptRemote(), true);
    }

    public function testIsAcceptRemote_private_machine() {
        putenv('HTTP_X_FORWARDED_FOR=124.35.221.2');
        $this->assertEquals(Util::isAcceptRemote(), false);
    }

    public function testIsAcceptRemote_現在のIP() {
        putenv('HTTP_X_FORWARDED_FOR=210.138.60.124');
        $this->assertEquals(Util::isAcceptRemote(), false);
    }

    public function testIsAcceptRemote_null() {
        putenv('HTTP_X_FORWARDED_FOR=');
        $this->assertEquals(Util::isAcceptRemote(null), false);
    }

    public function testIsAcceptRemote_空() {
        putenv('HTTP_X_FORWARDED_FOR=   ');
        $this->assertEquals(Util::isAcceptRemote(), false);
    }

    public function testIsAcceptRemote_任意() {
        putenv('HTTP_X_FORWARDED_FOR=テキストIP');
        $this->assertEquals(Util::isAcceptRemote(), true);
    }

    public function testGetEncode_UTF8() {
        $this->assertEquals(Util::getEncode('UTF-8'), 'UTF-8');
    }

    public function testGetEncode_SJIS() {
        $input = mb_convert_encoding('文字列', 'SJIS', 'UTF-8');
        $this->assertEquals(Util::getEncode($input), 'SJIS');
    }

    public function testConvertEncoding_SJIS() {
        $input = mb_convert_encoding('文字列', 'SJIS', 'UTF-8');
        $this->assertEquals(Util::convertEncoding($input), '文字列');
    }

    public function testConvertEncoding_UTF8() {
        $this->assertEquals(Util::convertEncoding('文字列'), '文字列');
    }

    public function testIsDefaultBRANDCoDomain_whenHTTPHostMatched() {
        $_SERVER['HTTP_HOST'] = 'brandcotest.com';
        $this->assertTrue(Util::isDefaultBRANDCoDomain());
    }

    public function testIsDefaultBRANDCoDomain_whenHTTPHostUnmatched() {
        $_SERVER['HTTP_HOST'] = 'INVALID';
        $this->assertFalse(Util::isDefaultBRANDCoDomain());
    }

    public function testIsDefaultManagerDomain_whenHTTPHostMatched() {
        $_SERVER['HTTP_HOST'] = 'manager.brandcotest.com';
        $this->assertTrue(Util::isDefaultManagerDomain());
    }

    public function testIsDefaultManagerDomain_whenHTTPHostUnmatched() {
        $_SERVER['HTTP_HOST'] = 'INVALID';
        $this->assertFalse(Util::isDefaultManagerDomain());
    }

    public function testGetMappedServerName_whenNotMappedAndDefaultDomain() {
        $_SERVER['HTTP_HOST'] = 'brandcotest.com';
        $this->assertEquals('brandcotest.com', Util::getMappedServerName());
    }

    public function testGetMappedServerName_whenNotMappedAndDefaultManagerDomain() {
        $_SERVER['HTTP_HOST'] = 'manager.brandcotest.com';
        $this->assertEquals('brandcotest.com', Util::getMappedServerName());
    }

    public function testGetMappedServerName_whenNotMappedAndNoBrandId() {
        $_SERVER['HTTP_HOST'] = 'test.com';
        $this->assertEquals('test.com', Util::getMappedServerName());
    }

    public function testGetMappedServerName_whenMappedAndBrandIdMatched() {
        $brand_id = 99;
        $this->assertEquals('test.com', Util::getMappedServerName($brand_id));
    }

    public function testGetMappedBrandId_whenHTTPHostMatchedWithClearCaches() {
        Util::clearCaches();
        $_SERVER['HTTP_HOST'] = 'test.com';
        $brand_id = 99;
        $this->assertEquals($brand_id, Util::getMappedBrandId());
    }

    public function testGetMappedBrandId_whenHTTPHostNotMatchedWithClearCaches() {
        Util::clearCaches();
        $_SERVER['HTTP_HOST'] = 'brandcotest.com';
        $this->assertEquals(Util::NOT_MAPPED_BRAND, Util::getMappedBrandId());
    }

    public function testConstructBaseURL_whenNotSecureAndDefaultDomainAndUseDirectoryName() {
        $brand_id = 1;
        $this->assertEquals('http://brandcotest.com/directory/', Util::constructBaseURL($brand_id, 'directory', false));
    }

    public function testConstructBaseURL_whenSecureAndMappedDomainAndWithoutDirectoryName() {
        $brand_id = 99;
        $this->assertEquals('https://test.com/', Util::constructBaseURL($brand_id, 'test.com', true));
    }

    public function testResolveDirectoryPath_whenNotMapped() {
        $brand_id = 1;
        $this->assertEquals('directory/', Util::resolveDirectoryPath($brand_id, 'directory'));
    }

    public function testResolveDirectoryPath_whenMappedAndMatchedTheDomain() {
        $brand_id = 99;
        $this->assertEquals('', Util::resolveDirectoryPath($brand_id, 'test.com'));
    }

    public function testResolveDirectoryPath_whenMappedAndNotMatchedTheDomain() {
        $brand_id = 99;
        $this->assertEquals('directory/', Util::resolveDirectoryPath($brand_id, 'directory'));
    }

    public function testGetCpURL_whenNotMapped() {
        $brand_id = 1;
        $this->assertEquals('http://brandcotest.com/directory/campaigns/1', Util::getCpURL($brand_id, 'directory', 1));
    }

    public function testGetCpURL_whenMapped() {
        $brand_id = 99;
        $this->assertEquals('http://test.com/campaigns/1', Util::getCpURL($brand_id, 'test.com', 1));
    }

    public function testHaveDirectoryName_whenNotMappedAndHasDirectory_withClearCache() {
        Util::clearCaches();
        $brand = new Brand();
        $brand->id = 1;
        $brand->directory_name = 'directory';
        $this->assertTrue(Util::haveDirectoryName($brand));
    }

    public function testHaveDirectoryName_whenMappedAndNoDirectory() {
        $brand = new Brand();
        $brand->id = 99;
        $brand->directory_name = 'test.com';
        $this->assertFalse(Util::haveDirectoryName($brand));
    }

    public function testIsMatchArray1() {
        $arr1 = array('aaa', 'bbb', 'ccc');
        $arr2 = array('aaa', 'bbb', 'ccc');
        $this->assertTrue(Util::isMatchArray($arr1, $arr2));
    }

    public function testIsMatchArray2() {
        $arr1 = array();
        $arr2 = array();
        $this->assertTrue(Util::isMatchArray($arr1, $arr2));
    }

    public function testIsMatchArray_notMatch1() {
        $arr1 = array('aaa', 'bbb');
        $arr2 = array('aaa', 'bbb', 'ccc');
        $this->assertFalse(Util::isMatchArray($arr1, $arr2));
    }

    public function testIsMatchArray_notMatch2() {
        $arr1 = array('aaa', 'bbb', 'ccc');
        $arr2 = array('aaa', 'bbb');
        $this->assertFalse(Util::isMatchArray($arr1, $arr2));
    }

    public function test_isIncludeArray() {
        $arr1 = array('aaa', 'bbb', 'ccc');
        $arr2 = array('aaa', 'bbb');
        $this->assertFalse(Util::isIncludeArray($arr1, $arr2));
    }

    public function test_isIncludeArray2() {
        $arr1 = array('aaa', 'bbb');
        $arr2 = array('aaa', 'bbb', 'ccc');
        $this->assertTrue(Util::isIncludeArray($arr1, $arr2));
    }

    public function test_isIncludeArray3() {
        $arr1 = array('aaa', 'bbb');
        $arr2 = array('aaa', 'bbb');
        $this->assertTrue(Util::isIncludeArray($arr1, $arr2));
    }

    public function test_isIncludeArray4() {
        $arr1 = array('aaa', 'あいうえお');
        $arr2 = array('aaa', 'あいうえお', 'ccc');
        $this->assertTrue(Util::isIncludeArray($arr1, $arr2));
    }

    public function test_isIncludeArray5() {
        $arr1 = array();
        $arr2 = array('aaa', 'bbb');
        $this->assertFalse(Util::isIncludeArray($arr1, $arr2));
    }

    public function test_isIncludeArray6() {
        $arr1 = array('aaa', 'bbb', 'ccc');
        $arr2 = array();
        $this->assertFalse(Util::isIncludeArray($arr1, $arr2));
    }

    public function test_isIncludeArray7() {
        $arr1 = array('KOSE');
        $arr2 = array('kose');
        $this->assertTrue(Util::isIncludeArray($arr1, $arr2));
    }

    public function test_isIncludeArray8() {
        $arr1 = array('KOSE', 'bbb');
        $arr2 = array('kose', 'bbb', 'ccc');
        $this->assertTrue(Util::isIncludeArray($arr1, $arr2));
    }

    public function test_isNullOrEmpty1_whenIsNull() {
        $this->assertTrue(Util::isNullOrEmpty(null));
    }

    public function test_isNullOrEmpty2_whenIsEmpty() {
        $this->assertTrue(Util::isNullOrEmpty(''));
    }

    public function test_isNullOrEmpty3_whenObject() {
        $this->assertFalse(Util::isNullOrEmpty(new DateTime()));
    }

    public function test_isNullOrEmpty4_whenNotNullString() {
        $this->assertFalse(Util::isNullOrEmpty('TEST'));
    }

    public function test_exsitNullOrEmpty1_whenEmptyArgument() {
        $this->assertFalse(Util::existNullOrEmpty());
    }

    public function test_exsitNullOrEmpty2_whenOneStringArgument() {
        $this->assertFalse(Util::existNullOrEmpty('TEST'));
    }

    public function test_exsitNullOrEmpty3_whenOneObjectArgument() {
        $this->assertFalse(Util::existNullOrEmpty(new DateTime()));
    }

    public function test_exsitNullOrEmpty4_whenOneNullArgument() {
        $this->assertTrue(Util::existNullOrEmpty(null));
    }

    public function test_exsitNullOrEmpty5_whenOneEmptyArgument() {
        $this->assertTrue(Util::existNullOrEmpty(''));
    }

    public function test_exsitNullOrEmpty6_whenTwoEmptyArgument() {
        $this->assertTrue(Util::existNullOrEmpty('', ''));
    }

    public function test_exsitNullOrEmpty7_whenOneStringAndOneEmptyArguments() {
        $this->assertTrue(Util::existNullOrEmpty('TEST', ''));
    }

    public function testIsInvalidBrandName01() {
        $this->assertFalse(Util::isInvalidBrandName('hoge'));
    }

    public function testIsInvalidBrandName02() {
        $this->assertFalse(Util::isInvalidBrandName('ブランド'));
    }

    public function testIsInvalidBrandName03() {
        $this->assertFalse(Util::isInvalidBrandName('テスト(てすと)'));
    }

    public function testIsInvalidBrandName04() {
        $this->assertFalse(Util::isInvalidBrandName('TEST(テスト)'));
    }

    public function testIsInvalidBrandName05() {
        $this->assertTrue(Util::isInvalidBrandName('テスト (TEST)'));
    }

    public function testIsInvalidBrandName06() {
        $this->assertTrue(Util::isInvalidBrandName('TEST(テスト テスト)'));
    }
}