<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.MultiPostSnsShare');

try {
    /** @var MultiPostSnsShare $multi_post_sns_share */
    $multi_post_sns_share = new MultiPostSnsShare();
    if (!($lock_file = Util::lockFile($multi_post_sns_share))) return;
    $multi_post_sns_share->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('MultiPostSnsShare Error.');
    $logger->error($e);
}
