<?php
// 設定ファイルの読みこみ
require_once(dirname(__FILE__).'/../apps/config/define.php');

AAFW::import('jp.aainc.aafw.web.aafwController');
AAFW::import('jp.aainc.classes.LoginUtil');
try{
    $controller = aafwController::getInstance('manager');

    print $controller->run();
} catch( Exception $e ) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error($e);
    header("HTTP/1.1 503 Service Temporarily Unavailable");
    header('Location:' .config('Static.Url') . '/503.html');
    //print "Fatal Error!";
}
