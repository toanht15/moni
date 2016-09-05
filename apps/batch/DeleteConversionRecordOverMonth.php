<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.DeleteConversionRecordOverMonth');

try {
    $obj = new DeleteConversionRecordOverMonth();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('DeleteConversionRecordOverMonth Error');
    $logger->error($e);
}
