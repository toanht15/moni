<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.SyncPageEntries');

try {
    $obj = new SyncPageEntries($argv[1]);
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('SyncPageEntries Error');
    $logger->error($e);
}
