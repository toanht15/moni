<?php

AAFW::import('jp.aainc.classes.services.ApplicationService');

abstract class GetFacebookPagePostInfoBatchBase
{
    const PARTITIONING_FACTOR = 7;

    const TYPE_INTERNAL = 1;
    const TYPE_EXTERNAL = 2;

    const FB_API_LIMIT_RECORD = 1000;
    const FB_REQUEST_LIMIT = 600;

    private $entries_tables = array(
        self::TYPE_INTERNAL => 'facebook_entries',
        self::TYPE_EXTERNAL => 'external_fb_entries'
    );

    protected $service_factory;
    protected $logger;
    protected $db;

    protected $facebookApiClient;
    protected $facebookUserTest;
    protected $request_count;
    protected $execute_class;

    public function __construct()
    {
        $config = aafwApplicationConfig::getInstance();
        $this->facebookUserTest = array(
            'userId' => $config->query('@facebook.AaIdFacebookTest.userId')
        );

        $this->facebookApiClient = new FacebookApiClient(FacebookApiClient::BRANDCO_MODE_USER);
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->execute_class = get_class($this);
        $this->db = aafwDataBuilder::newBuilder();
    }

    public function doProcess()
    {
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
     * テストユーザーのAccessTokenを取得する
     * @param $user_id
     * @return null
     */
    public function getFacebookAccessToken($user_id)
    {
        $data = null;

        if (!$user_id) {
            return $data;
        }

        /** @var UserApplicationService $user_application_service */
        $user_application_service = $this->service_factory->create('UserApplicationService');
        $user_application = $user_application_service->getUserApplicationByUserIdAndAppId($user_id, ApplicationService::BRANDCO);

        if (!$user_application->access_token || !$user_application->refresh_token || !$user_application->client_id) {
            return $data;
        }

        /** @var BrandcoAuthService $brandco_auth_service */
        $brandco_auth_service = $this->service_factory->create('BrandcoAuthService');
        $refresh_token_result = $brandco_auth_service->refreshAccessToken($user_application->refresh_token, $user_application->client_id);

        if ($refresh_token_result->result->status === Thrift_APIStatus::SUCCESS) {
            $sns_access_token_result = $brandco_auth_service->getSNSAccessToken($refresh_token_result->accessToken);
            if ($sns_access_token_result->result->status === Thrift_APIStatus::SUCCESS) {
                $data = $sns_access_token_result->socialAccessToken->snsAccessToken;
            }
        }
        return $data;
    }

    /**
     * DetailCrawlerUrlを更新する
     * @param $objectId
     * @param $type
     * @param $data_type
     * @param $url
     */
    public function updateDetailCrawlerUrl($objectId, $type, $data_type, $url)
    {
        // detail_crawler_urlテブールに更新する
        $updateUrl = array();
        $updateUrl['object_id'] = $objectId;
        $updateUrl['type'] = $type;
        $updateUrl['crawler_type'] = DetailCrawlerUrl::CRAWLER_TYPE_FACEBOOK;
        $updateUrl['data_type'] = $data_type;
        $updateUrl['url'] = $url;

        $sql = "INSERT INTO detail_crawler_urls(object_id,type,crawler_type,data_type,url,created_at,updated_at) VALUES";
        $sql .= "({$updateUrl['object_id']},{$updateUrl['type']},{$updateUrl['crawler_type']},{$updateUrl['data_type']},'{$updateUrl['url']}',NOW(),NOW())";
        $sql .= "ON DUPLICATE KEY UPDATE url = VALUES (url),updated_at = NOW()";

        $this->db->executeUpdate($sql);
    }

    /**
     * 全てエントリを取得する
     * @return array
     */
    public function getAllEntries()
    {
        $result = array();

        foreach (array_keys($this->entries_tables) as $entry_type) {
            $min_id = $this->getMinID($entry_type);
            $max_id = $this->getMaxID($entry_type);
            if ($max_id === 0) {
                $this->logger->warn("There are no data in the users table!");
                continue;
            }

            $entries = $this->getAllEntriesByType($entry_type,$min_id,$max_id);

            foreach ($entries as $entry) {
                $entry['type'] = $entry_type;
                $result[] = $entry;
            }
        }

        return $result;
    }

    /**
     * @param
     * @param $min_id
     * @param $max_id
     * @return array
     */
    public function getAllEntriesByType($entry_type, $min_id, $max_id)
    {
        list($start_id, $end_id) = $this->getDataRange($min_id,$max_id);

        $query = "SELECT post_id, object_id FROM " . $this->entries_tables[$entry_type] . " WHERE del_flg = 0 AND id BETWEEN " . $start_id . " AND " . $end_id;
        $entries = $this->db->getBySQL($query, array());

        return $entries;
    }

    /**
     * @param $min_id
     * @param $max_id
     * @return array
     */
    public function getDataRange($min_id,$max_id)
    {
        $partitioning_no = date("N");
        $partitioning_factor = self::PARTITIONING_FACTOR;

        $start_id = (int)(($max_id-$min_id) * ($partitioning_no - 1) / $partitioning_factor) + $min_id;
        $end_id = (int)(($max_id-$min_id) * $partitioning_no / $partitioning_factor) + $min_id;

        return array($start_id, $end_id);
    }

    /**
     * @param $entry_type
     * @return int
     */
    public function getMaxID($entry_type)
    {
        $value = $this->db->getBySQL("SELECT MAX(id) max_id FROM " . $this->entries_tables[$entry_type], array());
        $max_id = (int)$value[0]['max_id'];

        return $max_id;
    }

    /**
     * @param $entry_type
     * @return int
     */
    public function getMinID($entry_type){
        $value = $this->db->getBySQL("SELECT MIN(id) min_id FROM " . $this->entries_tables[$entry_type], array());
        $min_id = (int)$value[0]['min_id'];

        return $min_id;
    }

    abstract function executeProcess();
}