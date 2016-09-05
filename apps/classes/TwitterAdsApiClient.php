<?php

require_once('vendor/codebird-php/src/codebird.php');

AAFW::import('jp.aainc.vendor.autoload');

class TwitterAdsApiClient extends aafwObject{

    const TYPE_EMAIL        = 'EMAIL';
    const TYPE_DEVICE_ID    = 'DEVICE_ID';
    const TYPE_TWITTER_ID   = 'TWITTER_ID';
    const TYPE_HANDLE       = 'HANDLE';
    const TYPE_PHONE_NUMBER = 'PHONE_NUMBER';

    private $connection;

    /**
     * @param $consumerKey
     * @param $consumerSecret
     * @param null $accessToken
     * @param null $accessTokenSecret
     */
    public function __construct($accessToken = null, $accessTokenSecret = null)
    {
        $consumerKey = config('@twitter.Ads.ConsumerKey');
        $consumerSecret = config('@twitter.Ads.ConsumerSecret');

        \Codebird\Codebird::setConsumerKey($consumerKey,$consumerSecret);

        $this->connection = \Codebird\Codebird::getInstance();

        if ($accessToken) {
            $this->connection->setToken($accessToken, $accessTokenSecret);
        }
    }

    /**
     * @param $callbackUrl
     * @return string
     */
    public function getLoginUrl($callbackUrl)
    {
        $request_token = $this->connection->oauth_requestToken([
            'oauth_callback' => $callbackUrl
        ]);
        $this->connection->setToken($request_token->oauth_token, $request_token->oauth_token_secret);

        return $this->connection->oauth_authorize();
    }

    /**
     * TODO use executeRequest
     * @param $oauthVerifier
     * @return mixed
     */
    public function getAccessToken($oauthVerifier)
    {
        return $this->connection->oauth_accessToken(["oauth_verifier" => $oauthVerifier]);
    }

    /**
     * @param $request
     * @param $param
     * @return mixed
     * @throws \Exception
     */
    public function executeRequest($request, $param)
    {
        $response = $this->connection->$request($param);

        if (isset($response->errors[0]->message)) {
            throw new Exception($response->errors[0]->message);
        }

        if (isset($response->data)) {
            return $response->data;
        } else {
            return $response;
        }
    }

    /**
     * @param $accountId
     * @param array $campaignIds
     * @param bool $withDeleted
     * @return array
     */
    public function getLineItems($accountId, $campaignIds = [], $withDeleted = true)
    {
        //TODO paging && exception
        $params = [
            'account_id' => $accountId,
            'httpmethod' => 'GET',
            'with_deleted' => $withDeleted
        ];

        if (count($campaignIds)) {
            $params['campaign_ids'] = implode(',', $campaignIds);
        }

        return $this->executeRequest("ads_accounts_ACCOUNT_ID_lineItems", $params);
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function getAdsAccount($params = [])
    {
        return $this->executeRequest("ads_accounts", $params);
    }

    /**
     * @param $accountId
     * @param $lineItemId
     * @param bool $withDeleted
     * @return array
     */
    public function getPromotedTweetsByLineItemId($accountId, $lineItemId, $withDeleted = true)
    {
        $params = [
            'account_id' => $accountId,
            'httpmethod' => 'GET',
            'line_item_id' => $lineItemId,
            'with_deleted' => $withDeleted
        ];

        return $this->executeRequest("ads_accounts_ACCOUNT_ID_promotedTweets", $params);
    }

    /**
     * @param $accountId
     * @param $promotedTweetIds
     * @param \DateTime $startTime
     * @param \DateTime $endTime
     * @param string $granularity
     * @param null $metrics
     * @return array
     */
    public function getPromotedTweetsStats($accountId, $promotedTweetIds, \DateTime $startTime, \DateTime $endTime, $granularity = "TOTAL", $metrics = null)
    {
        $endTimeClone = clone $endTime;
        $params = [
            'account_id' => $accountId,
            'httpmethod' => 'GET',
            'promoted_tweet_ids' => is_array($promotedTweetIds) ? implode(',', $promotedTweetIds) : $promotedTweetIds,
            'start_time' => $startTime->format(DATE_ISO8601),
            'end_time' => $endTimeClone->modify("+1day")->format(DATE_ISO8601),
            'granularity' => $granularity
        ];

        if ($metrics) {
            $params['metrics']   = $metrics;
        }

        return $this->executeRequest("ads_stats_accounts_ACCOUNT_ID_promotedTweets", $params);
    }

    /**
     * @param $accountId
     * @param $lineItemIds
     * @param \DateTime $startTime
     * @param \DateTime $endTime
     * @param string $granularity
     * @param null $metrics
     * @return array
     */
    public function getLineItemsStats($accountId, $lineItemIds, \DateTime $startTime, \DateTime $endTime, $granularity = "TOTAL", $metrics = null)
    {
        $endTimeClone = clone $endTime;
        $params = [
            'account_id' => $accountId,
            'httpmethod' => 'GET',
            'line_item_ids' => is_array($lineItemIds) ? implode(',',$lineItemIds) : $lineItemIds,
            'start_time' => $startTime->format(DATE_ISO8601),
            'end_time' => $endTimeClone->modify("+1day")->format(DATE_ISO8601),
            'granularity' => $granularity
        ];

        if ($metrics) {
            $params['metrics']   = $metrics;
        }

        return $this->executeRequest("ads_stats_accounts_ACCOUNT_ID_lineItems", $params);
    }

    /**
     * TODO use executeRequest
     * @param $tweetId
     * @return mixed
     */
    public function getTweetById($tweetId)
    {
        $tweet = $this->connection->statuses_show_ID(['id' => $tweetId]);
        return $tweet;
    }

    /**
     * @param $accountId
     * @param array $campaignIds
     * @param bool $withDeleted
     * @return array
     */
    public function getCampaigns($accountId, $campaignIds = [], $withDeleted = true)
    {
        $params = [
            'account_id' => $accountId,
            'httpmethod' => 'GET',
            'with_deleted' => $withDeleted,
            'sort_by'   => 'updated_at-desc'
        ];

        if (count($campaignIds) > 0) {
            $params['campaign_ids'] = implode(',', $campaignIds);
        }

        return $this->executeRequest("ads_accounts_ACCOUNT_ID_campaigns", $params);
    }

    /**
     * @param $accountId
     * @param $campaignId
     * @param bool $withDeleted
     * @return array
     */
    public function getCampaignById($accountId, $campaignId, $withDeleted = true)
    {
        $params = [
            'account_id' => $accountId,
            'httpmethod' => 'GET',
            'campaign_id' => $campaignId,
            'with_deleted' => $withDeleted
        ];

        return $this->executeRequest("ads_accounts_ACCOUNT_ID_campaigns_CAMPAIGN_ID", $params);
    }

    /**
     * @param $accountId
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $metrics
     * @param string $granularity
     * @return array
     */
    public function getAccountStats($accountId, \DateTime $from, \DateTime $to, $metrics = "", $granularity = "TOTAL")
    {
        $endTimeClone = clone $to;
        $params = [
            'account_id' => $accountId,
            'start_time' => $from->format(DATE_ISO8601),
            'end_time'  => $endTimeClone->modify("+1day")->format(DATE_ISO8601),
            'httpmethod' => 'GET',
            'granularity' => $granularity
        ];

        if ($metrics) {
            $params['metrics']   = $metrics;
        }

        return $this->executeRequest("ads_stats_accounts_ACCOUNT_ID", $params);
    }

    /**
     * @param $accountId
     * @param $campaignIds
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $metrics
     * @param string $granularity
     * @param array $options
     * @return array
     */
    public function getCampaignStats($accountId, $campaignIds, \DateTime $from, \DateTime $to, $metrics = "", $granularity = "TOTAL", $options = [])
    {
        $endTimeClone = clone $to;
        $params = [
            'account_id' => $accountId,
            'campaign_ids' => is_array($campaignIds) ? implode(',',$campaignIds) : $campaignIds,
            'start_time' => $from->format(DATE_ISO8601),
            'end_time'  => $endTimeClone->modify("+1day")->format(DATE_ISO8601),
            'httpmethod' => 'GET',
            'granularity' => $granularity
        ];

        if ($metrics) {
            $params['metrics']   = $metrics;
        }

        $params = array_merge($params, $options);

        return $this->executeRequest("ads_stats_accounts_ACCOUNT_ID_campaigns", $params);
    }

    /**
     * @param $accountId
     * @param $entity
     * @param $entityIds
     * @param \DateTime $from
     * @param \DateTime $to
     * @param $metricGroups
     * @param $placement
     * @param string $granularity
     * @return mixed
     * @throws \Exception
     */
    public function getStatSync($accountId, $entity, $entityIds, \DateTime $from, \DateTime $to, $metricGroups, $placement = 'ALL_ON_TWITTER', $granularity = 'TOTAL')
    {
        $params = [
            'account_id'    => $accountId,
            'entity'        => $entity,
            'entity_ids'    => $entityIds,
            'start_time'    => $from->format("Y-m-d\T00:00:00O"),
            'end_time'      => $to->modify("+1day")->format("Y-m-d\T00:00:00O"),
            'granularity'   => $granularity,
            'metric_groups' => $metricGroups,
            'placement'     => $placement
        ];
        return $this->executeRequest("ads_stats_accounts_ACCOUNT_ID", $params);
    }

    /**
     * @param $accountId
     * @param $entity
     * @param $entityIds
     * @param \DateTime $from
     * @param \DateTime $to
     * @param $metricGroups
     * @param string $placement
     * @param string $granularity
     * @param array $optional
     * @return mixed
     * @throws \Exception
     */
    public function requestStatJob($accountId, $entity, $entityIds, \DateTime $from, \DateTime $to, $metricGroups, $placement = 'ALL_ON_TWITTER', $granularity = 'TOTAL', $optional = array())
    {
        $params = [
            'httpmethod'    => 'POST',
            'account_id'    => $accountId,
            'entity'        => $entity,
            'entity_ids'    => $entityIds,
            'start_time'    => $from->format("Y-m-d\T00:00:00O"),
            'end_time'      => $to->modify("+1day")->format("Y-m-d\T00:00:00O"),
            'granularity'   => $granularity,
            'metric_groups' => $metricGroups,
            'placement'     => $placement
        ];
        $params = array_merge($params, $optional);

        return $this->executeRequest('ads_stats_jobs_accounts_ACCOUNT_ID', $params);
    }

    /**
     * @param $accountId
     * @param array $optional
     * @return mixed
     * @throws \Exception
     */
    public function getJob($accountId, $optional = array())
    {
        $params = [
            'httpmethod'    => 'GET',
            'account_id'    => $accountId
        ];

        $params = array_merge($params, $optional);

        return $this->executeRequest('ads_stats_jobs_accounts_ACCOUNT_ID', $params);
    }

    /**
     * @param $zipfile
     * @return bool|string
     */
    public function unZip($zipfile)
    {
        if ($f = gzopen($zipfile, "r")) {
            $gzStr = gzread($f, 10000);
            gzclose($f);

            return $gzStr;
        }

        return false;
    }

    /**
     * @param $accountId
     * @param $name
     * @param $listType
     * @param $list
     * @return object
     * @throws \Exception
     */
    public function createTailoredAudiences($accountId, $name, $listType, $list) {
        $tailoredAudience = $this->createEmptyTailoredAudiences($accountId, $name, $listType);

        if (!isset($tailoredAudience->id)) {
            throw new \Exception('Can not create tailored audiences');
        }

        $filePath = $this->createListFile($list, $tailoredAudience->id, $listType);

        $uploadResponse = $this->uploadTONSingleChunk($filePath, 'text/csv');

        if (!isset($uploadResponse->Location) || !$uploadResponse->Location) {
            throw new \Exception('Can not upload TON single chunk file');
        }
        
        unlink($filePath);

        $updateResponse = $this->changeTailoredAudiences($accountId, $tailoredAudience->id, $uploadResponse->Location, 'ADD');

        $tailoredAudience->tailored_audience_change_id = $updateResponse->id;

        return $tailoredAudience;
    }

    /**
     * @param $accountId
     * @param $audienceId
     * @param $listType
     * @param $list
     * @return object
     * @throws \Exception
     */
    public function updateTailoredAudiences($accountId, $audienceId, $listType, $list) {

        $filePath = $this->createListFile($list, $audienceId, $listType);

        $uploadResponse = $this->uploadTONSingleChunk($filePath, 'text/csv');

        if (!isset($uploadResponse->Location) || !$uploadResponse->Location) {
            throw new \Exception('Can not upload TON single chunk file');
        }

        unlink($filePath);

        $updateResponse = $this->changeTailoredAudiences($accountId, $audienceId, $uploadResponse->Location, 'REPLACE');

        return $updateResponse;
    }

    /**
     * @param $file
     * @param $fileType
     * @return object
     */
    public function uploadTONSingleChunk($file, $fileType)
    {
        $params = [
            'bucket' => 'ta_partner',
            'Content-Type' => $fileType,
            'Content-Length' => filesize($file),
            'X-TON-Expires' => (new \DateTime('+ 7 days'))->format(DATE_RFC1123),
            'media' => $file
        ];

        return $this->executeRequest('ton_bucket_BUCKET', $params);
    }

    /**
     * @param $list
     * @param $tailoredAudienceId
     * @param $listType
     * @return string
     * @throws \Exception
     */
    public function createListFile($list, $tailoredAudienceId, $listType) {
        $filePath = '/tmp/'.$tailoredAudienceId.'_' . uniqid() .'.csv';

        $regex = [
            self::TYPE_HANDLE => '/^[a-z0-9_]+$/',
            self::TYPE_TWITTER_ID => '/^\d+$/',
            self::TYPE_PHONE_NUMBER => '/^\d+$/',
            self::TYPE_EMAIL => '/^[a-z0-9][a-z0-9_\-\.\+]+\@[a-z0-9][a-z0-9\.]+[a-z]$/',
            self::TYPE_DEVICE_ID => '/^[a-z0-9][a-z0-9\-]+[a-z0-9]$/'
        ];

        //create or update csv file
        $handle = fopen($filePath, 'w+');
        if (!$handle) {
            throw new \Exception('can not open file '.$filePath);
        }

        foreach ($list as $item) {
            if (!preg_match($regex[$listType], $item)) {
                fclose($handle);
                throw new \Exception('Invalid data type: '.$item);
            }

            fwrite($handle, hash('sha256', $item).'\r\n');
        }

        fclose($handle);

        return $filePath;
    }

    /**
     * @param $accountId
     * @param $name
     * @param $listType
     * @return object
     */
    public function createEmptyTailoredAudiences($accountId, $name, $listType)
    {
        $params = [
            'account_id' => $accountId,
            'name'       => $name,
            'list_type'  => $listType
        ];
        return $this->executeRequest("ads_accounts_ACCOUNT_ID_tailoredAudiences", $params);
    }

    /**
     * @param $accountId
     * @param $audienceId
     * @param $filePath
     * @param $operation
     * @return object
     */
    public function changeTailoredAudiences($accountId, $audienceId, $filePath, $operation = 'ADD')
    {
        $params = [
            'account_id'            => $accountId,
            'tailored_audience_id'  => $audienceId,
            'input_file_path'       => $filePath,
            'operation'             => $operation
        ];

        return $this->executeRequest("ads_accounts_ACCOUNT_ID_tailoredAudienceChanges", $params);
    }

    /**
     * @param $accountId
     * @param $updatedAudiencesId //tailored audience change id
     * @return object
     */
    public function getTailoredAudienceChange($accountId, $updatedAudiencesId)
    {
        $params = [
            'account_id'    => $accountId,
            'id'            => $updatedAudiencesId
        ];

        return $this->executeRequest("ads_accounts_ACCOUNT_ID_tailoredAudienceChanges_ID", $params);
    }

    public function fetchAdsAccountsInfo($account_ids, $ads_user) {

        $accounts = $this->getAdsAccount();

        $return_data = array();

        foreach ($accounts as $account) {

            if(in_array($account->id, $account_ids)) {

                $extra_data = array(
                    'salt' => $account->salt,
                    'approval_status' => $account->approval_status,
                );

                $account_info = array(
                    "ads_user_id" => $ads_user->id,
                    "account_id" => $account->id,
                    "account_name" => $account->name,
                    "social_app_id" => SocialApps::PROVIDER_TWITTER,
                    "extra_data" => json_encode($extra_data),
                );

                $return_data[] = $account_info;
            }
        }

        return $return_data;
    }
}
