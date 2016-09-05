<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.LongRunningQueryChecker');
AAFW::import('jp.aainc.classes.batch.AutoUpdateCpStatusToClosed');

try {
    $cps_batch = new LongRunningQueryChecker();
    if (!($lock_file = Util::lockFile($cps_batch))) return;
    $cps_batch->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('LongRunningQueryChecker Error');
    $logger->error($e);
}
