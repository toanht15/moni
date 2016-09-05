<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.PanelDetailWriter');


try {
    $writer = new PanelDetailWriter();
    if (!($lock_file = Util::lockFile($writer))) return;
    $writer->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('PanelDetailWriter Error');
    $logger->error($e);
}