<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

/**
 * 1週間以上前のデータを削除しMailQueueテーブルの肥大化を防ぐ
 * MailQueueテーブルのデータについては毎日テーブルダンプを実行しバックアップを6ヶ月分保持しておく
 *
 * Class MailQueueRotator
 */
class MailQueueRotator extends BrandcoBatchBase {

    public function executeProcess() {
        $db = aafwDataBuilder::newBuilder();

        try {

            $delete_target_datetime = $this->getTargetDateTime();

            $result = $db->executeUpdate("
                /* MailQueueRotator */
                DELETE FROM mail_queues WHERE del_flg = 1 AND created_at < '{$delete_target_datetime}'");

            if (!$result) {
                throw new aafwException("DELETE FROM mail_queues failed!");
            }

        } catch(Exception $e) {
            $msg = "execution failed!: reason=" . json_encode($e, JSON_PRETTY_PRINT);
            aafwLog4phpLogger::getDefaultLogger()->error($msg);
            aafwLog4phpLogger::getHipChatLogger()->error($msg);
        }
    }

    /**
     * 1週間前の日時を返す
     * 時刻は00:00:00固定
     */
    public function getTargetDateTime() {
        $delete_target_date = date('Y-m-d', strtotime('-3 day'));
        return $delete_target_date . ' 00:00:00';
    }
}
