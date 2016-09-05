<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.UpdateCampaignPhotos');

try {
    $obj = new UpdateCampaignPhotos();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('UpdateCampaignPhotos Error');
    $logger->error($e);
}
