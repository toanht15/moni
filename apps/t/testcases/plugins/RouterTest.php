<?php

require_once preg_replace( '#/$#', '', AAFW_DIR ) . '/plugins/controller/Router.php';

class RouterTest extends BaseTest {

    const PARAM_GET = 1;
    const MAPPING_BRAND_ID = 99;

    protected function setUp() {
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
    }

    public function testDoService01_when_getParamHasAction_and_directoryName_and_package() {
        $controller = $this->newController('user');
        $controller->rewriteParams(array(),
            array('action' => 'action1', 'directory_name' => 'directory', 'package' => 'package1'),
            array(), array(), array(), array(), array());
        $target = new Router($controller);

        $target->doService();

        $get = $controller->getParams()[self::PARAM_GET];
        $this->assertEquals(
            array('{"action":"action1","directory_name":"directory","package":"brandco\/package1"}', BrandInfoContainer::getInstance()->getBrand()),
            array(json_encode($get), null));
    }

    public function testDoService02_when_notMapped_and_directoryNameExists_and_notReplaceTopPage() {
        $this->executeQuery("UPDATE brands SET directory_name = ''");
        $brand = $this->entity('Brands', array(
            'directory_name' => 'directory'
        ));
        $this->entity('BrandPageSettings', array(
            'brand_id' => $brand->id,
            'top_page_url' => ''
        ));
        $controller = $this->newController('user');
        $controller->rewriteParams(array(), array(), array(), array(), array(), array(), array('REQUEST_URI' => '/directory/campaigns'));
        $target = new Router($controller);

        $target->doService();

        $get = $controller->getParams()[self::PARAM_GET];
        $this->assertEquals(
            array('{"__path":["directory","campaigns"],"directory_name":"directory","req":null,"exts":[],"action":"campaigns","package":"brandco\/"}',
                BrandInfoContainer::getInstance()->getBrand()->id),
            array(json_encode($get), $brand->id));
    }

    public function testDoService03_when_notMapped_and_noDirectoryName_and_notReplaceTopPage() {
        $this->executeQuery("UPDATE brands SET directory_name = ''");
        $brand = $this->entity('Brands', array(
            'directory_name' => 'directory'
        ));
        $this->entity('BrandPageSettings', array(
            'brand_id' => $brand->id,
            'top_page_url' => ''
        ));
        $controller = $this->newController('user');
        $controller->rewriteParams(array(), array(), array(), array(), array(), array(), array('REQUEST_URI' => '/api/versions'));
        $target = new Router($controller);

        $target->doService();

        $get = $controller->getParams()[self::PARAM_GET];
        $this->assertEquals(
            array('{"__path":["api","versions"],"directory_name":null,"req":null,"exts":[],"action":"versions","package":"api"}', null),
            array(json_encode($get), null));
    }

    public function testDoService04_when_notMapped_and_directoryNameExists_and_replaceTopPage() {
        $this->executeQuery("UPDATE brands SET directory_name = ''");
        $brand = $this->entity('Brands', array(
            'directory_name' => 'directory'
        ));

        $this->deleteEntities('BrandPageSettings', array('brand_id' => $brand->id));
        $this->entity('BrandPageSettings', array(
            'brand_id' => $brand->id,
            'top_page_url' => '/directory/campaigns'
        ));
        $controller = $this->newController('user');
        $controller->rewriteParams(array(), array(), array(), array(), array(), array(), array('REQUEST_URI' => '/directory'));
        $target = new Router($controller);

        $target->doService();

        $get = $controller->getParams()[self::PARAM_GET];
        $this->assertEquals(
            array('{"__path":["directory","campaigns"],"directory_name":"directory","req":null,"exts":[],"action":"campaigns","package":"brandco\/"}', $brand->id),
            array(json_encode($get), BrandInfoContainer::getInstance()->getBrand()->id));
    }

    public function testDoService05_when_notMapped_and_directoryNameExists_and_noBrand() {
        $controller = $this->newController('user');
        $controller->rewriteParams(array(), array(), array(), array(), array(), array(), array('REQUEST_URI' => '/NOT_MATCHED'));
        $target = new Router($controller);

        $target->doService();

        $get = $controller->getParams()[self::PARAM_GET];
        $this->assertEquals(
            array('{"__path":["NOT_MATCHED"],"directory_name":"NOT_MATCHED","req":null,"exts":[],"action":"index","package":"brandco\/"}', null),
            array(json_encode($get), BrandInfoContainer::getInstance()->getBrand()));
    }

    public function testDoService06_when_mapped_and_noDirectoryName_and_notPassThrough() {
        $this->executeQuery("UPDATE brands SET directory_name = ''");
        $brand = $this->entity('Brands', array(
            'id' => self::MAPPING_BRAND_ID,
            'directory_name' => 'test.com'
        ));
        $_SERVER['HTTP_HOST'] = 'test.com';
        $controller = $this->newController('user');
        $controller->rewriteParams(array(), array(), array(), array(), array(), array(), array('REQUEST_URI' => '/campaigns'));
        $target = new Router($controller);

        $target->doService();

        $get = $controller->getParams()[self::PARAM_GET];
        $this->assertEquals(
            '{"__path":{"1":"campaigns"},"directory_name":null,"req":null,"exts":[],"action":"campaigns","package":"brandco\/"}',
            json_encode($get));
    }

    public function testDoService07_when_mapped_and_directoryName_and_notPassThrough() {
        $this->executeQuery("UPDATE brands SET directory_name = ''");
        $brand = $this->entity('Brands', array(
            'id' => self::MAPPING_BRAND_ID,
            'directory_name' => 'directory'
        ));
        $_SERVER['HTTP_HOST'] = 'test.com';
        $controller = $this->newController('user');
        $controller->rewriteParams(array(), array(), array(), array(), array(), array(), array('REQUEST_URI' => '/directory/campaigns'));
        $target = new Router($controller);

        $target->doService();

        $get = $controller->getParams()[self::PARAM_GET];
        $this->assertEquals(
            array('{"__path":["directory","campaigns"],"directory_name":"directory","req":null,"exts":[],"action":"campaigns","package":"brandco\/"}', $brand->id),
            array(json_encode($get), BrandInfoContainer::getInstance()->getBrand()->id));
    }

    public function testDoService08_when_mapped_and_noDirectoryName_and_passThrough() {
        $this->executeQuery("UPDATE brands SET directory_name = ''");
        $brand = $this->entity('Brands', array(
            'id' => self::MAPPING_BRAND_ID,
            'directory_name' => 'test.com'
        ));
        $_SERVER['HTTP_HOST'] = 'test.com';
        $controller = $this->newController('user');
        $controller->rewriteParams(array(), array(), array(), array(), array(), array(), array('REQUEST_URI' => '/auth/callback'));
        $target = new Router($controller);

        $target->doService();

        $get = $controller->getParams()[self::PARAM_GET];
        $this->assertEquals(
            array('{"__path":["auth","callback"],"directory_name":null,"req":null,"exts":[],"action":"callback","package":"auth"}', $brand->id),
            array(json_encode($get), BrandInfoContainer::getInstance()->getBrand()->id));
    }


    public function testDoService09_when_mapped_and_noDirectoryName_and_replaceTopPage() {
        $this->executeQuery("UPDATE brands SET directory_name = ''");
        $brand = $this->entity('Brands', array(
            'id' => self::MAPPING_BRAND_ID,
            'directory_name' => 'test.com'
        ));

        $this->deleteEntities('BrandPageSettings', array('brand_id' => self::MAPPING_BRAND_ID));
        $this->entity('BrandPageSettings', array(
            'brand_id' => self::MAPPING_BRAND_ID,
            'top_page_url' => '/campaigns'
        ));
        $_SERVER['HTTP_HOST'] = 'test.com';
        $controller = $this->newController('user');
        $controller->rewriteParams(array(), array(), array(), array(), array(), array(), array('REQUEST_URI' => '/'));
        $target = new Router($controller);

        $target->doService();

        $get = $controller->getParams()[self::PARAM_GET];
        $this->assertEquals(
            array('{"__path":{"1":"campaigns"},"directory_name":null,"req":null,"exts":[],"action":"campaigns","package":"brandco\/"}', $brand->id),
            array(json_encode($get), BrandInfoContainer::getInstance()->getBrand()->id));
    }

    public function testDoService10_when_mapped_and_directoryNameExists_and_replaceTopPage() {
        $this->executeQuery("UPDATE brands SET directory_name = ''");
        $brand = $this->entity('Brands', array(
            'id' => self::MAPPING_BRAND_ID,
            'directory_name' => 'directory'
        ));

        $this->deleteEntities('BrandPageSettings', array('brand_id' => self::MAPPING_BRAND_ID));
        $this->entity('BrandPageSettings', array(
            'brand_id' => self::MAPPING_BRAND_ID,
            'top_page_url' => '/directory/campaigns'
        ));
        $_SERVER['HTTP_HOST'] = 'test.com';
        $controller = $this->newController('user');
        $controller->rewriteParams(array(), array(), array(), array(), array(), array(), array('REQUEST_URI' => '/directory'));
        $target = new Router($controller);

        $target->doService();

        $get = $controller->getParams()[self::PARAM_GET];
        $this->assertEquals(
            array('{"__path":["directory","campaigns"],"directory_name":"directory","req":null,"exts":[],"action":"campaigns","package":"brandco\/"}', $brand->id),
            array(json_encode($get), BrandInfoContainer::getInstance()->getBrand()->id));
    }
}