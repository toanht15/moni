<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.SendImperfectCpNoticeToAlliedId');

try {
    /** @var SendImperfectCpNoticeToAlliedId $sender */
    $sender = new SendImperfectCpNoticeToAlliedId();
    if (!($lock_file = Util::lockFile($sender))) return;
    $sender->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('SendImperfectCpNoticeToAlliedId Error');
    $logger->error($e);
}