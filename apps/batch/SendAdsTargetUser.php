<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.SendAdsTargetUser');

try {
    $obj = new SendAdsTargetUser($argv);
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('SendAdsTargetUser Error');
    $logger->error($e);
}