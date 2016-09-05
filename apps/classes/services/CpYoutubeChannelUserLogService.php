<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.vendor.google2.Client');
AAFW::import('jp.aainc.vendor.google2.Service.YouTube');

class CpYoutubeChannelUserLogService extends aafwServiceBase {

    protected $cp_yt_channel_user_log;

    public function __construct() {
        $this->cp_yt_channel_user_log = $this->getModel('CpYoutubeChannelUserLogs');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_action_id
     * @param $cp_user_id
     * @return mixed
     */
    public function getLog($cp_action_id, $cp_user_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'cp_user_id' => $cp_user_id
        );
        return $this->cp_yt_channel_user_log->findOne($filter);
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getLogs($cp_action_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
        );
        return $this->cp_yt_channel_user_log->find($filter);
    }

    /**
     * @return mixed
     */
    public function createEmptyLog() {
        return $this->cp_yt_channel_user_log->createEmptyObject();
    }

    /**
     * @param CpYoutubeChannelUserLog $log
     */
    public function saveLog(CpYoutubeChannelUserLog $log) {
        $this->cp_yt_channel_user_log->save($log);
    }

    /**
     * @param $cp_action_id
     * @param $cp_user_id
     * @param $status
     */
    public function setLog($cp_action_id, $cp_user_id, $status) {

        $log = $this->getLog($cp_action_id, $cp_user_id);

        if (!$log) {
            $log = $this->createEmptyLog();
            $log->cp_action_id = $cp_action_id;
            $log->cp_user_id = $cp_user_id;
        }
        $log->status = $status;

        $this->saveLog($log);
    }

    public function deletePhysicalInstagramFlowUserLogsByCpActionId ($cp_action_id) {
        if ($cp_action_id) {
            $logs = $this->cp_yt_channel_user_log->find(array('cp_action_id' => $cp_action_id));
            foreach ($logs as $log) {
                $this->cp_yt_channel_user_log->deletePhysical($log);
            }
        }
    }

    /**
     * @param $access_token
     * @param $channel_id
     * @return Google_Service_YouTube_Subscription
     */
    public function subscribeYoutubeChannel($access_token, $channel_id) {

        $status_log = CpYoutubeChannelUserLog::STATUS_ERROR;

        try {
            $client = $this->initClient();
            $client->setAccessToken($access_token);

            $youtube = new Google_Service_YouTube($client);

            $response = $this->insertChannel($youtube, $channel_id);

            $status_log = $response ? CpYoutubeChannelUserLog::STATUS_FOLLOWED : CpYoutubeChannelUserLog::STATUS_FOLLOWING;

        } catch (Exception $e) {

            aafwLog4phpLogger::getDefaultLogger()->error("subscribeYoutubeChannel Exception");
            aafwLog4phpLogger::getDefaultLogger()->error($e);

        } finally {
            return $status_log;
        }
    }

    /**
     * @return Google_Client
     * @throws Exception
     */
    public function initClient() {

        try {
            $client = new Google_Client();
            $client->setClientId(config('@google.User.ClientID'));
            $client->setClientSecret(config('@google.User.ClientSecret'));
            $client->setRedirectUri(Util::getHttpProtocol().'://'. Util::getMappedServerName() . '/'.config('@google.User.RedirectUri'));
            $scope = array();
            $apiBase = config('@google.User.ApiBaseUrl');
            foreach (config('@google.User.Scope') as $url) {
                array_push($scope, $apiBase.'/'.$url);
            }
            $client->setScopes($scope);
            return $client;
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error("subscribeYoutubeChannel#initClient Exception");
            throw $e;
        }
    }

    /**
     * @param Google_Service_YouTube $youtube
     * @param $channel_id
     * @return Google_Service_YouTube_Subscription
     * @throws Exception
     */
    private function insertChannel(Google_Service_YouTube $youtube, $channel_id) {

        try {
            $resource_id = new Google_Service_YouTube_ResourceId();
            $resource_id->setChannelId($channel_id);
            $resource_id->setKind('youtube#channel');

            $subscription_snippet = new Google_Service_YouTube_SubscriptionSnippet();
            $subscription_snippet->setResourceId($resource_id);

            $subscription = new Google_Service_YouTube_Subscription();
            $subscription->setSnippet($subscription_snippet);

            return $youtube->subscriptions->insert('snippet', $subscription, array());
        } catch (Google_Service_Exception $e) {
            if ($e->getCode() == 400 && $e->getErrors()[0]['reason'] == 'subscriptionDuplicate') {
                return null;
            } else {
                aafwLog4phpLogger::getDefaultLogger()->error("subscribeYoutubeChannel#insertChannel Exception");
                throw $e;
            }
        }
    }

    public function deletePhysicalYoutubeChannelUserLogsByCpActionId($cp_action_id) {
        if (!$cp_action_id) {
            return;
        }
        $logs = $this->getLogs($cp_action_id);
        if (!$logs) {
            return;
        }
        foreach ($logs as $log) {
            $this->cp_yt_channel_user_log->deletePhysical($log);
        }
    }

    public function deletePhysicalYoutubeChannelUserLogsByCpActionIdAndCpUserId($cp_action_id, $cp_user_id) {
        if (!$cp_action_id || !$cp_user_id) {
            return;
        }
        $logs = $this->cp_yt_channel_user_log->find(array('cp_action_id' => $cp_action_id, 'cp_user_id' => $cp_user_id));
        if (!$logs) {
            return;
        }
        foreach ($logs as $log) {
            $this->cp_yt_channel_user_log->deletePhysical($log);
        }
    }

    /**
     * @param $cp_action_id
     * @param $cp_user_ids
     * @return array|null
     */
    public function getLogsByCpUserIds($cp_action_id, $cp_user_ids) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'cp_user_id' => $cp_user_ids
        );

        $logs = $this->cp_yt_channel_user_log->find($filter);
        $user_log = array();
        foreach ($logs as $log) {
            $log->status_string = CpYoutubeChannelUserLog::$youtube_status_string[$log->status];
            $user_log[$log->cp_user_id] = $log;
        }
        return $user_log;
    }

    public function getYoutubeChannelSubscriberCount($accessToken, $channelId) {
        $client = $this->initClient();
        $client->setAccessToken($accessToken);
        $youtube = new Google_Service_YouTube($client);
        $res = $youtube->channels->listChannels('statistics', array('id' => $channelId));

        return $res['modelData']['items'][0]['statistics']['subscriberCount'];
    }
}
