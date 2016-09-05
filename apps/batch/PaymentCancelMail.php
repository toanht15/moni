<?php
require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.PaymentCancelMail');

try {
    $obj = new PaymentCancelMail();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->run();
} catch (Exception $e) {
    aafwLog4phpLogger::getDefaultLogger()->error($e);
}
