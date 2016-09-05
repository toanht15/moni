<?php
require_once '../../tracker/public/router.php';

class TrackRouterTest extends PHPUnit_Framework_TestCase {
    const base_path = '/var/www/html/brandco/tracker/public/../actions/';
    /**
     * @test
     * .phpがない場合
     */
    public function getPathAndClassNameTestTracker() {
        $router = new Router();
        $_SERVER['REQUEST_URI'] = '/open_email_tracker?params=hogehoge';
        list($path, $class_name) = $router->getPathAndClassName();
        $this->assertEquals($path, self::base_path.'open_email_tracker.php');
        $this->assertEquals($class_name, 'OpenEmailTracker');
    }

    /**
     * @test
     * .phpがある場合
     */
    public function getPathAndClassNameTestTrackerphp() {
        $router = new Router();
        $_SERVER['REQUEST_URI'] = '/open_email_tracker.php?params=hogehoge';
        list($path, $class_name) = $router->getPathAndClassName();
        $this->assertEquals($path, self::base_path.'open_email_tracker.php');
        $this->assertEquals($class_name, 'OpenEmailTracker');
    }

    /**
     * @test
     * .phpががない+/
     */
    public function getPathAndClassNameTestTracker1() {
        $router = new Router();
        $_SERVER['REQUEST_URI'] = '/open_email_tracker/?params=hogehoge';
        list($path, $class_name) = $router->getPathAndClassName();
        $this->assertEquals($path, self::base_path.'open_email_tracker.php');
        $this->assertEquals($class_name, 'OpenEmailTracker');
    }

    /**
     * @test
     * .phpががある+/
     */
    public function getPathAndClassNameTestTrackerphp1() {
        $router = new Router();
        $_SERVER['REQUEST_URI'] = '/open_email_tracker.php/?params=hogehoge';
        list($path, $class_name) = $router->getPathAndClassName();
        $this->assertEquals($path, self::base_path.'open_email_tracker.php');
        $this->assertEquals($class_name, 'OpenEmailTracker');
    }

    /**
     * @test
     * request_uri = /hogehoge
     */
    public function getPathAndClassNameTesthoge() {
        $router = new Router();
        $_SERVER['REQUEST_URI'] = '/hogehoge?params=hogehgoe';
        list($path, $class_name) = $router->getPathAndClassName();
        $this->assertEquals($path, self::base_path.'hogehoge.php');
        $this->assertEquals($class_name, 'Hogehoge');
    }

    /**
     * @test
     * request_url = /
     */
    public function getPathAndClassNameTest() {
        $router = new Router();
        $request_uri = '/';
        list($path, $class_name) = $router->getPathAndClassName($request_uri);
        $this->assertEquals($path, null);
        $this->assertEquals($class_name, null);
    }

    /**
     * @runInSeparateProcess
     * @test
     * データベースで確認する
     */
    public function RouterRunTest() {
        $_SERVER['REQUEST_URI'] = '/open_email_tracker';
        $_GET['params'] = base64_encode(json_encode(array('user_id' => 16, 'cp_action_id' => 1538)));
        putenv('HTTP_USER_AGENT=test agent');
        putenv('HTTP_REFERER=test ref');
        putenv('HTTP_ACCEPT_LANGUAGE=test lang');
        putenv('HTTP_X_FORWARDED_FOR=192.168.50.1');
        $_GET['sp_mode'] = 'on';
        $router = new Router();
        $router->run();
    }

    /**
     * @runInSeparateProcess
     * @test
     * データベースで確認する
     */
    public function RouterRunTestPHP() {
        $_SERVER['REQUEST_URI'] = '/open_email_tracker.php/';
        $_GET['params'] = base64_encode(json_encode(array('user_id' => 22, 'cp_action_id' => 1538)));
        putenv('HTTP_USER_AGENT=test agent');
        putenv('HTTP_REFERER=test ref');
        putenv('HTTP_ACCEPT_LANGUAGE=test lang');
        putenv('HTTP_X_FORWARDED_FOR=192.168.50.1');
        $_GET['sp_mode'] = 'on';
        $router = new Router();
        $router->run();
    }
}
