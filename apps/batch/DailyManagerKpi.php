<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.DailyManagerKpi');

try {
    $obj = new DailyManagerKpi();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess($argv);
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('DailyManagerKpi Error');
    $logger->error($e);
}