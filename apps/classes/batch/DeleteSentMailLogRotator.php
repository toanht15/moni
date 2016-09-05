<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
require_once (dirname(__FILE__) . '/../services/UserMailTrackingService.php');

/**
 * WARNING:
 * まだ未完成なので使用しないでください。
 * sent_mail_logsのレコード数が多くなり、
 * 各週 (各日) 毎のデータに移行可能なバッチが完成次第、実行します。
 */
class DeleteSentMailLogRotator extends BrandcoBatchBase {

    public function executeProcess() {
        
        $user_mail_tracking_service = new UserMailTrackingService();
        $delete_target_datetime     = $this->getTargetDateTime();
        
        try {
        //抽出された削除すべきuser_mailsのid
        $user_mail_ids = $user_mail_tracking_service->findUserMailIdsBeforeLimitDate($delete_target_datetime);
        
        if (!$user_mail_ids) {
            throw new aafwException("DELETE FROM user_mails _failed!");
        }

        //各テーブルの対象レコードを削除
        foreach ($user_mail_ids as $user_mail_id) {
            $user_mail = $user_mail_tracking_service->findUserMail($user_mail_id);
            $user_mail_tracking_service->deletePhysicalUserMail($user_mail);

            $user_mail = $user_mail_tracking_service->findWelcomeMail($user_mail_id);
            $user_mail_tracking_service->deletePhysicalWelcomeMail($user_mail);

            $user_mail = $user_mail_tracking_service->findEntryMail($user_mail_id);
            $user_mail_tracking_service->deletePhysicalEntryMail($user_mail);

            $user_mail = $user_mail_tracking_service->findOpenUserMailTrackingLog($user_mail_id);
            $user_mail_tracking_service->deletePhysicalOpenUserMailTrackingLog($user_mail);
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
        $delete_target_date = date('Y-m-d', strtotime('-7 day'));
        return $delete_target_date . ' 00:00:00';
    }
}