<?php
require_once('vendor/Merazoma/driver/Driver.php');
require_once('vendor/Merazoma/driver/WebDriver.php');
require_once('vendor/Merazoma/Client.php');

class UtilityApiClient {

    private static $instance = null;
    private $client;

    // const RTOASTER = 1; //2015-09-07 使わなくなったためコメントアウト
    const ADEBIS                = 2;
    const LIVE800               = 3;
    const REPLACE_TAG           = 4;
    const TRACKER               = 5;
    const REPLACE_ANNOUNCE_TAG  = 6;

    public function __construct() {
        $this->settings = aafwApplicationConfig::getInstance();
        $driver = new \Merazoma\driver\WebDriver(config('UtilityAPI'), array(CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4));
        $this->client = new \Merazoma\Client($driver);
    }

    public static function getInstance(){
        if (self::$instance == null) self::$instance = new UtilityApiClient();
        return self::$instance;
    }

    /**
     * monipla_user_id の token 取得
     * @param $client_id
     * @param $monipla_user_id
     * @return mixed
     */
    public function getUserToken($client_id, $monipla_user_id) {
        return $this->client->get('/user', array(
            'clientId' => $client_id,
            'userId' => $monipla_user_id))->token;
    }

    public function getUser($client_id, $token) {
        return $this->client->get('/user', array(
            'clientId' => $client_id,
            'token' => $token
        ));
    }

    /**
     * brands_users_relations id の token 取得
     * @param $client_id
     * @param $brands_users_relation_id
     * @return mixed
     */
    public function getBrandsUserRelationToken($client_id, $brands_users_relation_id) {
        return $this->client->get('/brandco_user', array(
            'clientId' => $client_id,
            'userId' => $brands_users_relation_id))->token;
    }

}
