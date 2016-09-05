<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class LongRunningQueryChecker extends BrandcoBatchBase {

    function executeProcess() {
        $builder = aafwDataBuilder::newBuilder();
        $result = $builder->executeUpdate("
                SELECT
                    thd_id,
                    conn_id,
                    user,
                    db,
                    state,
                    time,
                    current_statement,
                    last_statement,
                    last_statement_latency,
                    lock_latency,
                    rows_examined,
                    rows_sent,
                    rows_affected,
                    tmp_tables,
                    tmp_disk_tables,
                    full_scan
                FROM
                    ps_helper.processlist
                WHERE
                    time >= 10 AND command = 'Query'");
        if ($result === false) {
            throw new aafwException("Query Execution failed!");
        }
        while ($row = $builder->fetchResultSet($result)) {
            $time = $row['time'];

            if ($time >= 10 && $time <= 600) {
                $msg = "実行時間が閾値を超えるクエリが存在するで注意してください！: " . json_encode($row, JSON_PRETTY_PRINT);
                aafwLog4phpLogger::getDefaultLogger()->warn($msg);
                aafwLog4phpLogger::getHipChatLogger()->warn($msg);
            } else if ($time > 600) {
                $msg = "実行時間の閾値を大きく超えるクエリが存在します！ 今すぐ停止の検討をしてください!!!: " . json_encode($row, JSON_PRETTY_PRINT);
                aafwLog4phpLogger::getDefaultLogger()->error($msg);
                aafwLog4phpLogger::getHipChatLogger()->error($msg);
                $mail = new MailManager(array('FromAddress'=> 'bc-dev@aainc.co.jp'));
                $mail->Subject = "実行時間が閾値を大きく超えるクエリがみつかりました。";
                $mail->loadMailContent('long_running_query_checker');
                $settings = aafwApplicationConfig::getInstance();
                $mailAddress = $settings->Mail['ALERT']['CcAddress'];

                $mail->sendNow($mailAddress, array(
                    'BAD_QUERY' => json_encode($row, JSON_PRETTY_PRINT)
                ));
            }
        }
    }
}