<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.UpdateMoniplaCpInfoManager');

try {
    aafwLog4phpLogger::getDefaultLogger()->info('UpdateMoniplaCpInfo start');
    $obj = new UpdateMoniplaCpInfoManager();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess();
    aafwLog4phpLogger::getDefaultLogger()->info('UpdateMoniplaCpInfo end');
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('UpdateMoniplaCpInfo Error');
    $logger->error($e);
}