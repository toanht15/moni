<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.OldPsessCleaner');

try {
    $obj = new OldPsessCleaner();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->execute();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('OldPsessCleaner Error');
    $logger->error($e);
}
