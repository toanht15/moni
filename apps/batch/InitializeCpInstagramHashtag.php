<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.InitializeCpInstagramHashtag');

try {
    $insta = new InitializeCpInstagramHashtag();
    if (!($lock_file = Util::lockFile($insta))) return;
    $insta->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('InitializeCpInstagramHashtag Error');
    $logger->error($e);
}
