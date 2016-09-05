<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.StoreSocialAccountFollowerCount');
AAFW::import('jp.aainc.classes.batch.CountDailyYoutubeChannelSubscriber');

try {
    $obj = new StoreSocialAccountFollowerCount();
    $countDailyYoutubeChannelSubscriber = new CountDailyYoutubeChannelSubscriber();

    if (!($lock_file = Util::lockFile($obj))) return;
    $countDailyYoutubeChannelSubscriber->doProcess();
    $obj->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('StoreSocialAccountFollowerCount Error');
    $logger->error($e);
}
