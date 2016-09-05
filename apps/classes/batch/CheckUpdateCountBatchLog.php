<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.classes.services.UpdateCountBatchLogService');

class CheckUpdateCountBatchLog extends BrandcoBatchBase {

    private $batchs = array(
        'UpdateCpAnnounceCount',
        'UpdateCpEntryCount',
        'UpdateMsgDeliveredCount',
        'UpdateMsgReadCount',
    );

    function executeProcess() {
        $updateCountBatchLogService = $this->service_factory->create('UpdateCountBatchLogService');
        $date = date('Y-m-d', strtotime(' -1 day'));
        $failBatchs = array();
        foreach($this->batchs as $batch) {
            if(!$updateCountBatchLogService->getBatchLog($batch,$date,UpdateCountBatchLog::SUCCESS_STATUS)) {
                $failBatchs[] = array('batch_name' => $batch);
            }
        }
        $this->sendAlert($failBatchs);
    }

    private function sendAlert($failBatchs) {
        if (!count($failBatchs)) return;
        $date = date('Y-m-d', strtotime(' -1 day'));
        //Send Mail
        $mailParams = array(
            'DATE' => $date,
            'FAIL_BATCH' => $failBatchs,
        );
        $mail = new MailManager(array('FromAddress'=> 'bc-dev@aainc.co.jp'));
        $mail->loadMailContent('alert_fail_batch');
        $settings = aafwApplicationConfig::getInstance();
        $mailAddress = $settings->Mail['ALERT']['CcAddress'];
        $mail->sendNow($mailAddress, $mailParams);
        //Send Hipchat
        $this->hipchat_logger->error($date.'に失敗した計測バッチ: ' . json_encode($failBatchs));
    }
}