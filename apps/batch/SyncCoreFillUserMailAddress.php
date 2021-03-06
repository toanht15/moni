<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.SyncCoreFillUserMailAddress');

try {
    $obj = new SyncCoreFillUserMailAddress();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('SyncCoreFillUserMailAddress Error');
    $logger->error($e);
}