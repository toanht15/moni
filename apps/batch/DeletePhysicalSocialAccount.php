<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.DeletePhysicalSocialAccount');

try {
    $obj = new DeletePhysicalSocialAccount();
    $name = get_class($obj);
    if (!($lock_file = Util::lockFileByName($name))) return;
    $obj->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('DeletePhysicalSocialAccount Error');
    $logger->error($e);
}
