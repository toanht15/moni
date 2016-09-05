<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.AccessTokenReGetter');

try {
    $token = new AccessTokenReGetter();
    if (!($lock_file = Util::lockFile($token))) return;
    $token->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('ReGetAccessToken Error');
    $logger->error($e);
}
