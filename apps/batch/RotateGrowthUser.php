<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.DailyBatchRunner');

try {
    $classPath = 'jp.aainc.classes.batch.RotateGrowthUser';
    $obj = new DailyBatchRunner($classPath, $argv);
    $name = get_class($obj) . $classPath;
    if (!($lock_file = Util::lockFileByName($name))) return;
    $obj->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('RotateGrowthUser Error');
    $logger->error($e);
}