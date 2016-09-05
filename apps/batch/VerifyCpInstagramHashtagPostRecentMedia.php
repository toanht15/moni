<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.VerifyCpInstagramHashtagPostRecentMedia');

try {
    $insta = new VerifyCpInstagramHashtagPostRecentMedia($argv);
    if (!($lock_file = Util::lockFile($insta))) return;
    $insta->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('VerifyCpInstagramHashtagPostRecentMedia Error');
    $logger->error($e);
}
