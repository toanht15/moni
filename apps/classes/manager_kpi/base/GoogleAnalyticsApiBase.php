<?php
AAFW::import('jp.aainc.vendor.google.Google_Client');
AAFW::import('jp.aainc.vendor.google.contrib.Google_AnalyticsService');

abstract class GoogleAnalyticsApiBase {

    protected $client;
    protected $analytics_service;
    protected $view_id;

    public function __construct() {
        $this->init();
    }

    /**
     * @return Google_Client
     */
    private function init() {
        $client_email = config('@google.Google.ClientEmail');
        //$client_email = '855348886040-72j27q8g0urmdm63r0aaod508tpi90c1@developer.gserviceaccount.com';

        $private_key = @file_get_contents(DOC_CONFIG . DIRECTORY_SEPARATOR . config('@google.Google.P12KeyFile'));

        $scopes = array(config('@google.Google.ApiBaseUrl') . "/analytics.readonly");

        $credential = new Google_AssertionCredentials($client_email, $scopes, $private_key);

        $this->client = new Google_Client();
        $this->client->setAssertionCredentials($credential);

        $this->analytics_service = new Google_AnalyticsService($this->client);

        $this->view_id = "ga:" . config('@google.Google.ViewId');
    }

    /**
     * @param $view_id
     * @param $from
     * @param $to
     * @param $metrics
     * @param array $option
     * @return mixed
     */
    protected function get($view_id, $from, $to, $metrics, $option = array()) {
        if(count($option)) {
            $result = $this->analytics_service->data_ga->get($view_id, $from, $to, $metrics, $option);
        }else{
            $result = $this->analytics_service->data_ga->get($view_id, $from, $to, $metrics);
        }
        return $result;
    }

    /**
     * @param null $path
     * @return null|string
     */
    protected function getFilterPath($path = null) {
        return $path ? 'ga:pagePath=~' . $path . '.' : null;
    }

    /**
     * @param $date
     * @return mixed
     */
    abstract function doExecute($date);
}