<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.FixPersonalInfoFlg');

try {
    $obj = new FixPersonalInfoFlg();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess();

} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('FixPersonalInfoFlg Error');
    $logger->error($e);
}