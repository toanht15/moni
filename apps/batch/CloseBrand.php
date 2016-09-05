<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.CloseBrand');

try {
    $obj = new CloseBrand();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->closeProcess();
    $obj->updateStatusSiteClose();
    $obj->deleteProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('CloseBrand Error');
    $logger->error($e);
}