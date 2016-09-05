<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
require_once('vendor/codebird-php/src/codebird.php');

abstract class GetTwitterDataBatchBase {

    const TYPE_INTERNAL = 1;
    const TYPE_EXTERNAL = 2;

    const TWITTER_RATE_LIMIT_WAITING = 900; //waiting time to avoid rate limit

    const RATE_LIMIT_ERROR_CODE = 88;

    protected $service_factory;
    protected $logger;
    protected $db;
    protected $request_count;
    protected $execute_class;
    protected $twitter;
    protected $rate_limit_exceeded;
    protected $crawler_twitter_log_service;

    protected $twitter_crawler_apps;
    protected $current_twitter_app;
    protected $used_app_count;

    public function __construct() {
        $this->service_factory = new aafwServiceFactory();
        $this->execute_class = get_class($this);
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->db = aafwDataBuilder::newBuilder();

        $this->request_count = 0;
        $this->rate_limit_exceeded = false;
        $this->crawler_twitter_log_service = $this->service_factory->create('CrawlerTwitterLogService');
    }

    public function doProcess() {
        try {
            $this->logger->warn("start batch: class=" . $this->execute_class);
            $start_time = date("Y-m-d H:i:s");
            $this->executeProcess();
            $end_time = date("Y-m-d H:i:s");

            $this->logger->warn($this->execute_class . ' Status:Success Start_Time:' . $start_time . ' End_Time:' . $end_time);
        } catch (Exception $e) {
            $end_time = date("Y-m-d H:i:s");
            $this->logger->warn($this->execute_class . ' Status:Error Start_Time:' . $start_time . ' End_Time:' . $end_time . ' Detail:' . $e);
        }
    }

    /**
     * @param $array
     * @param $id
     * @return mixed
     */
    public function removeDuplicateElementById($array, $id) {
        if (!is_array($array) || Util::isNullOrEmpty($id)) {
            return $array;
        }

        foreach ($array as $key => $value) {
            if ($value['id'] == $id) {
                unset($array[$key]);
                break;
            }
        }
        return $array;
    }

    /**
     * @param $count
     * @param $since_id
     * @param $max_id
     * @param array $id
     * @return array
     */
    public function createParameterForTwitterApi($count, $since_id, $max_id, $id = array()) {
        $request_parameter = array('count' => $count);

        if (count($id) > 0) {
            $request_parameter = array_merge($request_parameter, $id);
        }

        if (!Util::isNullOrEmpty($since_id)) {
            $request_parameter['since_id'] = $since_id;
        }

        if (isset($max_id)) {
            $request_parameter['max_id'] = $max_id;
        }

        return $request_parameter;
    }

    /**
     * App AuthでTwitter Clientを作成する
     *
     * @param $consumer_key
     * @param $consumer_secret
     * @return \Codebird\Codebird
     * @throws Exception
     */
    protected function initTwitterClient($consumer_key, $consumer_secret) {
        \Codebird\Codebird::setConsumerKey(
            $consumer_key,
            $consumer_secret
        );
        $client = \Codebird\Codebird::getInstance();
        $client->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);
        $reply = $client->oauth2_token();
        $bearer_token = $reply['access_token'];

        if (Util::isNullOrEmpty($bearer_token)) {
            return null;
        }

        \Codebird\Codebird::setBearerToken($bearer_token);
        return $client;
    }

    /**
     * @return bool
     */
    protected function build() {
        $current_twitter_app = $this->getCurrentTwitterApp();
        $this->twitter = $this->initTwitterClient($current_twitter_app['ConsumerKey'], $current_twitter_app['ConsumerSecret']);

        if (!$this->twitter) {
            return false;
        }
        return true;
    }

    /**
     * @param $table
     * @return array
     */
    public function getMinMaxID($table) {
        $value = $this->db->getBySQL("SELECT MAX(id) max_id, MIN(id) min_id FROM " . $table, array());
        $max_id = (int)$value[0]['max_id'];
        $min_id = (int)$value[0]['min_id'];

        return array($min_id, $max_id);
    }

    /**
     * @param $response
     * @return bool
     */
    protected function getErrorMessage($response) {
        if ($response['httpstatus'] != 200) {
            return $response['errors'][0]['message'];
        }
        return null;
    }

    /**
     * @param $response
     * @return mixed
     */
    protected function getErrorCode($response){
        if ($response['httpstatus'] != 200) {
            return $response['errors'][0]['code'];
        }
        return null;
    }

    /**
     * @return mixed
     */
    protected function getCurrentTwitterApp() {
        if (!$this->current_twitter_app) {
            //Init used_app_count
            $this->used_app_count = 0;
            $this->fetchNewTwitterApp();
        }

        return $this->current_twitter_app;
    }

    /**
     * @return null
     */
    protected function fetchNewTwitterApp() {
        if (!$this->twitter_crawler_apps || !is_array($this->twitter_crawler_apps)) {
            $this->logger->error("{$this->execute_class}: No app to crawler !");
            return null;
        }
        $app_config = array_shift($this->twitter_crawler_apps);
        array_push($this->twitter_crawler_apps, $app_config);

        $this->used_app_count++;

        //Reset API request count
        $this->request_count = 0;
        $this->current_twitter_app = $app_config;
    }

    /**
     * @param $last_id
     * @param $crawler_type
     * @param $type
     */
    protected function updateCrawlerTwitterLog($last_id, $type, $crawler_type) {
        $today = date('Y-m-d');

        $last_crawler_twitter = $this->crawler_twitter_log_service->getCrawlerTwitterLog($today, $type, $crawler_type);

        if (!$last_crawler_twitter) {
            $last_crawler_twitter = $this->crawler_twitter_log_service->createEmptyCrawlerTwitterLog();
            $last_crawler_twitter->batch_date = $today;
            $last_crawler_twitter->crawler_type = $crawler_type;
            $last_crawler_twitter->type = $type;
        }

        $last_crawler_twitter->last_id = $last_id;

        $this->crawler_twitter_log_service->updateCrawlerTwitterLog($last_crawler_twitter);
    }

    /**
     * @param $crawler_type
     * @param $type
     * @return mixed
     */
    protected function getLastCrawlerObjectByDate ($type, $crawler_type){
        $batch_date = date('Y-m-d');

        return $this->crawler_twitter_log_service->getCrawlerTwitterLog($batch_date, $type , $crawler_type);
    }

    abstract function executeProcess();
}