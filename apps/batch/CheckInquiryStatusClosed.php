<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.CheckInquiryStatusClosed');

try {
    $obj = new CheckInquiryStatusClosed();
    $name = get_class($obj) . $argv[1];
    if (!($lock_file = Util::lockFileByName($name))) return;
    $obj->doProcess($argv);
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('CheckInquiryStatusClosed Error');
    $logger->error($e);
}
