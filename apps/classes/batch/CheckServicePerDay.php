<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.classes.services.ManagerKpiService');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class CheckServicePerDay extends BrandcoBatchBase{

    public function executeProcess() {
        try {
            $body =$this->checkService();
            if(count($body)) {
                $contents = array(
                    'ALERT' => $body,
                );
                $mailManager = new MailManager();
                $mailManager->loadMailContent('check_service');
                $settings = aafwApplicationConfig::getInstance();
                $mail_address = $settings->Mail['ALERT']['CcAddress'];
                $mailManager->sendNow($mail_address, $contents);
            }

            $this->checkConversionTagGrowthRate();

        } catch (Exception $e) {
            $this->logger->error($e);
            $this->logger->error($e->getMessage());
            throw $e;
        }
    }

    private function checkService() {
        $date = date('Y-m-d', strtotime('-1 days'));
        $data_builder = aafwDataBuilder::newBuilder();
        $failure_values = $data_builder->getBySQL("
            SELECT mkc.name
              FROM manager_kpi_columns mkc
              WHERE (
                SELECT 1
                  FROM manager_kpi_values
                  WHERE column_id = mkc.id AND summed_date = '{$date}' AND value > 0
              ) IS NULL", array());
        $body = '';
        foreach ($failure_values as $entity) {
            $body .= "- " . $entity['name'] . PHP_EOL;
        }
        return $body;
    }

    /**
     * コンバージョンタグ件数の増加をチェックし、前日比で5%減っていた場合にAlertを出します
     */
    private function checkConversionTagGrowthRate() {
        $yesterday = date('Y-m-d', strtotime('-1 days'));
        $day_before_yesterday = date('Y-m-d', strtotime('-2 days'));

        $managerKpiService = new ManagerKpiService();

        //40はコンバージョンタグカラムID
        $yesterday_cv_count = $managerKpiService->getManagerKpiValueByColumnIdAndDate(40,$yesterday)->value;
        $day_before_yesterday_cv_count = $managerKpiService->getManagerKpiValueByColumnIdAndDate(40,$day_before_yesterday)->value;

        if ($yesterday_cv_count <= 0 || $day_before_yesterday_cv_count <= 0) {
            return;
        }

        $growth_value = $yesterday_cv_count - $day_before_yesterday_cv_count;
        if ($growth_value >= 0) {
            return;
        }

        // 成長比率
        $growth_rate = ($growth_value * -100) / $day_before_yesterday_cv_count;
        if ($growth_rate > 5) {
            $this->hipchat_logger->error('■コンバージョン数が前日に比べて5%以上減っています。確認してください!' );
        }
    }
}