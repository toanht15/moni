<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.SessionViewer');

try {
    $obj = new SessionViewer();
    $obj->setSessionId($argv[1]);
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('SessionViewer Error');
    $logger->error($e);
}