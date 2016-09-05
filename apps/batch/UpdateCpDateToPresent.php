<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.UpdateCpDateToPresent');

try {
    $cps_batch = new UpdateCpDateToPresent();
    if (!($lock_file = Util::lockFile($cps_batch))) return;
    $cps_batch->executeProcess($argv);
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('UpdateCpDateToPresent Error');
    $logger->error($e);
}
