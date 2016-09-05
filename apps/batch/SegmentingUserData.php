<?php
require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.SegmentingUserData');

try {
    $sender = new SegmentingUserData($argv);
    if (!($lock_file = Util::lockFile($sender))) return;
    $sender->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('SegmentingUserData Error');
    $logger->error($e);
}