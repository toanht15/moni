<?php
// 設定ファイルの読みこみ
require_once(dirname(__FILE__).'/../apps/config/define.php');
AAFW::import('jp.aainc.aafw.web.aafwController');
AAFW::import('jp.aainc.classes.LoginUtil');
try{
    if (config('Maintenance') && !Util::isManagerIp()) {
        header("HTTP/1.1 503 Service Temporarily Unavailable");
        header('Location:' . config('Static.Url') . (Util::isSmartPhone() ? '/maintenance_sp.html' : '/maintenance.html') );
        exit();
        }
    $controller = aafwController::getInstance('user');
    print $controller->run();

} catch( Exception $e ) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error($e);
    header("HTTP/1.1 503 Service Temporarily Unavailable");
    header('Location:' .config('Static.Url') . '/503.html');
    //print "Fatal Error!";
}