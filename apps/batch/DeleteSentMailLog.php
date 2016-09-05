<?php
require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.DeleteSentMailLogRotator');

/**
 * WARNING:
 * まだ未完成なので使用しないでください。
 * sent_mail_logsのレコード数が多くなり、
 * 各週 (各日) 毎のデータに移行可能なバッチが完成次第、実行します。
 */
try {
    // 実行されると困るのでExceptionを投げる
    throw new Exception('まだ未完成なので使用しないでください');

    $obj = new DeleteSentMailLogRotator();
    $name = get_class($obj);
    if (!($lock_file = Util::lockFileByName($name))) return;
    $obj->doProcess();
} catch (Exception $e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('DeleteSentMailLogRotator Error');
    $logger->error($e);
}