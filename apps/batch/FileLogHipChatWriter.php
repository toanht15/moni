<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.FileLogHipChatWriter');

try {
    // テスト用
    if (extension_loaded('newrelic')) {
        $config = aafwApplicationConfig::getInstance();
        if($config->NewRelic['use']) {
            newrelic_set_appname($config->NewRelic['batchApplicationName']);
            newrelic_name_transaction("FileLogHipChatWriter");
        }
    }

    $obj = new FileLogHipChatWriter();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess($argv);
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('FileLogHipChatWriter Error');
    $logger->error($e);
}