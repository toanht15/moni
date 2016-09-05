<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.SubmittedAtModifier');


try {
    $bat = new SubmittedAtModifier();
    if (!($lock_file = Util::lockFile($bat))) return;
    $bat->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('SubmittedAtModifier Error');
    $logger->error($e);
}