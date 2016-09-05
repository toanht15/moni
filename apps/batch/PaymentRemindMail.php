<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.PaymentRemindMail');

try {
    $obj = new PaymentRemindMail();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->execute();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('PaymentRemindMail Error');
    $logger->error($e);
}