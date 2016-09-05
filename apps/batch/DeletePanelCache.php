<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.PanelCacheManager');


try {
    $obj = new PanelCacheManager();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->deleteAll();
    //$obj->deleteByBrandId(1);
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $this->logger->error('PanelCacheManager Error');
    $this->logger->error($e);
}