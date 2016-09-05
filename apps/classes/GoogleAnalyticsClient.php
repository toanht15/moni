<?php
AAFW::import('jp.aainc.vendor.google.Google_Client');
AAFW::import('jp.aainc.vendor.google.contrib.Google_AnalyticsService');

class GoogleAnalyticsClient {

    const FILTER_EXACT_MATCH = 1;   //正確なバリューで絞り込み
    const FILTER_CONTAIN_MATCH = 2; //含まれているバリューで絞り込み

    const RATE_LIMIT_REASON = "dailyLimitExceeded";

    protected $analytics_service;
    protected $view_id;
    protected $logger;

    public function __construct() {
        $this->init();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     *  Init Google Analytics Service
     */
    private function init() {
        $client_email = config('@google.Google.ClientEmail');
        $private_key = @file_get_contents(DOC_CONFIG . DIRECTORY_SEPARATOR . config('@google.Google.P12KeyFile'));

        $scopes = array(config('@google.Google.ApiBaseUrl') . "/analytics.readonly");
        $credential = new Google_AssertionCredentials($client_email, $scopes, $private_key);

        $client = new Google_Client();
        $client->setAssertionCredentials($credential);

        $this->analytics_service = new Google_AnalyticsService($client);
        $this->view_id = "ga:" . config('@google.Google.ViewId');
    }

    /**
     * @param $dimension
     * @param $value
     * @param int $type
     * @return null|string
     */
    public function buildDimensionFilter($dimension, $value, $type = self::FILTER_EXACT_MATCH) {
        if (Util::isNullOrEmpty($dimension) || Util::isNullOrEmpty($value)) {
            return null;
        }

        return $type == self::FILTER_EXACT_MATCH ? $dimension . '==' . $value : $dimension . '=~' .$value;
    }

    /**
     * 日付ページビュー数を取得する
     * @param $date
     * @param $filters
     * @return null
     */
    public function getPageViewsByDate($date, $filters = array()) {
        try {
            $from_date = date("Y-m-d", strtotime($date));
            $to_date = date("Y-m-d", strtotime($date));

            //ページビューメトリクス
            $metrics = "ga:pageviews";
            $option = array(
                "sort" => "-ga:pageviews",
            );

            //複数filters条件がある場合は、「;」で分ける(AND女意見)
            if (count($filters) > 0) {
                $option['filters'] = implode(";", $filters);
            }

            $obj = $this->get($this->view_id, $from_date, $to_date, $metrics, $option);
            list($result) =  $obj['rows'];

            return $result[0] ?: 0;

        } catch (Exception $e) {
            $this->logger->error('GoogleAnalyticsClient#getPageViewsByDate error: ' . $e);
            return $this->getErrorMessage($e);
        }
    }

    /**
     * @param $view_id
     * @param $from
     * @param $to
     * @param $metrics
     * @param array $option
     * @return mixed
     */
    public function get($view_id, $from, $to, $metrics, $option = array()) {
        if (count($option)) {
            $result = $this->analytics_service->data_ga->get($view_id, $from, $to, $metrics, $option);
        } else {
            $result = $this->analytics_service->data_ga->get($view_id, $from, $to, $metrics);
        }
        return $result;
    }

    /**
     * 日付ユニークユーザー数を取得する
     * @param $date
     * @param array $filters
     * @return null
     */
    public function getUniqueUserByDate($date, $filters = array()) {
        try {
            $from_date = date("Y-m-d", strtotime($date));
            $to_date = date("Y-m-d", strtotime($date));

            //ユーザー数メトリクス
            $metrics = "ga:users";
            $option = count($filters) == 0 ? array() : array("filters" => implode(";", $filters));

            $obj = $this->get($this->view_id, $from_date, $to_date, $metrics, $option);
            list($result) =  $obj['rows'];

            return $result[0] ?: 0;
        } catch (Exception $e) {
            $this->logger->error('GoogleAnalyticsClient#getUserByDate error: ' . $e);
            return $this->getErrorMessage($e);
        }
    }

    /**
     * エラーメッセージを取得する
     * @param $message
     * @return mixed
     */
    public function getErrorMessage($message) {
        return $message['errors'][0];
    }
}