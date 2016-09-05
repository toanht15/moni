<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.PaymentCompleteMail');

try {
    $obj = new PaymentCompleteMail();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->run();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('PaymentCompleteMail Error');
    $logger->error($e);
}
