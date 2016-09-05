<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.manager_kpi.base.GoogleAnalyticsApiBase');

class GaUsers extends GoogleAnalyticsApiBase implements IManagerKPI {

    function doExecute($date) {
        try {
            //取得する期間
            $from = date("Y-m-d", strtotime($date));
            $to = date("Y-m-d", strtotime($date));

            //取得するデータの組み合わせ
            $metrics = "ga:users";  //メトリクスの設定

            //データの取得
            $obj = $this->get($this->view_id, $from, $to, $metrics);
            list($result) = $obj['rows'];

            return $result[0] ? $result[0] : 0;
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('pv_kpi_batch get error: ' . $e);
            return 0;
        }
    }
}