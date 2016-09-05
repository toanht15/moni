<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.UpdateCpInstagramFollowUserLogStatus');

try {
    $obj = new UpdateCpInstagramFollowUserLogStatus();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('UpdateCpInstagramFollowUserLogStatus Error');
    $logger->error($e);
}