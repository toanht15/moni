<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.MailQueueMailSender');

try {
    $sender = new MailQueueMailSender();
    if (!($lock_file = Util::lockFile($sender))) return;
    $sender->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('MailQueueMailSender Error');
    $logger->error($e);
}