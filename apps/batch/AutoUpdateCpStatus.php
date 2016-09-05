<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.AutoUpdateCpStatus');
AAFW::import('jp.aainc.classes.batch.AutoUpdateCpStatusToClosed');

try {
    $cps_batch = new AutoUpdateCpStatus();
    if (!($lock_file = Util::lockFile($cps_batch))) return;
    $cps_batch->doProcess();
    $cps_for_closed_batch = new AutoUpdateCpStatusToClosed();
    $cps_for_closed_batch->doProcess();

} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('AutoUpdateCpStatus Error');
    $logger->error($e);
}
