<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.MigrateProfileHistories');


try {
    $bat = new MigrateProfileHistories();
    if (!($lock_file = Util::lockFile($bat))) return;
    $bat->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('MigrateProfileHistories Error');
    $logger->error($e);
}