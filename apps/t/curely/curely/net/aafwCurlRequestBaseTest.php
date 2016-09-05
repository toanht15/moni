<?php
require_once __DIR__ . '/../../../../config/define.php';
AAFW::import('jp.aainc.aafw.net.aafwCurlRequestBase');

class aafwCurRequestBaseTest extends PHPUnit_Framework_TestCase {
    private $target = null;

    public function setup() {
        $this->target = new aafwCurlRequestBase();
    }

    /**
     * @test
     */
    public function testBuildCurlOptions() {
        $this->assertEquals(array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_USERAGENT => 'aafw',
            //CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_URL => 'http://hoge/fuga.html?name=value',
            CURLOPT_HTTPHEADER => array(
                'header1',
                'header2',
            ),
        ), $this->target->buildCurlOptions(
            'GET',
            'http://hoge/fuga.html',
            array('name' => 'value'),
            array('header1', 'header2')
        ));

        $this->assertEquals(array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_USERAGENT => 'aafw',
            //CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_URL => 'http://hoge/fuga.html?hogefuga=1&name=value',
            CURLOPT_HTTPHEADER => array(
                'header1',
                'header2',
            ),
        ), $this->target->buildCurlOptions(
            'GET',
            'http://hoge/fuga.html?hogefuga=1',
            array('name' => 'value'),
            array('header1', 'header2')
        ));
        $this->assertEquals(array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_USERAGENT => 'aafw',
            //CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_URL => 'http://hoge/fuga.html',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query(array('name' => 'value'), null, '&'),
        ), $this->target->buildCurlOptions(
            'POST',
            'http://hoge/fuga.html',
            array('name' => 'value')
        ));

        $this->assertEquals(array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_USERAGENT => 'aafw',
            //CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_URL => 'http://hoge/fuga.html',
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_POSTFIELDS => http_build_query(array('name' => 'value'), null, '&'),
        ), $this->target->buildCurlOptions(
            'DELETE',
            'http://hoge/fuga.html',
            array('name' => 'value')
        ));

        $this->assertEquals(array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_USERAGENT => 'aafw',
            //CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_URL => 'http://hoge/fuga.html',
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => http_build_query(array('name' => 'value'), null, '&'),
        ), $this->target->buildCurlOptions(
            'PUT',
            'http://hoge/fuga.html',
            array('name' => 'value')
        ));
    }
}
