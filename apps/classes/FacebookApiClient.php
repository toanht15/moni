<?php
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.FacebookSession');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.FacebookRedirectLoginHelper');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.FacebookRequest');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.FacebookSDKException');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.FacebookRequestException');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.FacebookAuthorizationException');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.FacebookServerException');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.FacebookOtherException');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.FacebookPermissionException');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.HttpClients.FacebookHttpable');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.HttpClients.FacebookCurlHttpClient');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.HttpClients.FacebookCurl');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.GraphObject');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.GraphSessionInfo');
AAFW::import('jp.aainc.vendor.facebook.php-sdk-v4.src.Facebook.FacebookResponse');
AAFW::import('jp.aainc.classes.CacheManager');

use Facebook\FacebookAuthorizationException as FacebookAuthorizationException;
use Facebook\FacebookClientException as FacebookClientException;
use Facebook\FacebookOtherException as FacebookOtherException;
use Facebook\FacebookPermissionException as FacebookPermissionException;
use Facebook\FacebookRedirectLoginHelper as FacebookRedirectLoginHelper;
use Facebook\FacebookRequest as FacebookRequest;
use Facebook\FacebookSDKException as FacebookSDKException;
use Facebook\FacebookServerException as FacebookServerException;
use Facebook\FacebookSession as FacebookSession;
use Facebook\FacebookThrottleException as FacebookThrottleException;

class FacebookApiClient {
    const TOKEN_EXPIRED_EXCEPTION = "Session has expired, or is not valid for this app.";
    const BRANDCO_MODE_USER = 'user';
    const BRANDCO_MODE_ADMIN = 'admin';
    const BRANDCO_MODE_MARKETING_ADMIN = 'marketing';
    const BATCH_REQUEST_MAX = 50;
    const PARAM_PER_CHILD_REQUEST_MAX = 20;

    private $token;
    protected $service_factory;
    protected $logger;
    protected $helper;
    protected $session;
    protected $scopes;
    protected $appProperties;
    /** @var FacebookStreamService $stream_service */
    protected $stream_service;

    public function __construct($brandco_mode = self::BRANDCO_MODE_ADMIN) {
        $config = aafwApplicationConfig::getInstance();
        if ($brandco_mode == self::BRANDCO_MODE_ADMIN) {
            $this->appProperties = array(
                'appId' => $config->query('@facebook.Admin.AppId'),
                'secret' => $config->query('@facebook.Admin.AppSecretKey')
            );
            $this->scopes = $config->query('@facebook.Admin.Scope');
        } else if ($brandco_mode == self::BRANDCO_MODE_USER){
            $this->appProperties = array(
                'appId' => $config->query('@facebook.User.AppId'),
                'secret' => $config->query('@facebook.User.AppSecretKey')
            );
            $this->scopes = $config->query('@facebook.User.Scope');
        } else if ($brandco_mode == self::BRANDCO_MODE_MARKETING_ADMIN) {
            $this->appProperties = array(
                'appId' => $config->query('@facebook.MarketingAdmin.AppId'),
                'secret' => $config->query('@facebook.MarketingAdmin.AppSecretKey')
            );
            $this->scopes = $config->query('@facebook.MarketingAdmin.Scope');
        }
        FacebookSession::setDefaultApplication($this->appProperties['appId'], $this->appProperties['secret']);

        $this->service_factory = new aafwServiceFactory ();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->stream_service = $this->service_factory->create('FacebookStreamService');
    }

    /**
     * @param $callback_url
     */
    public function setRedirectLoginHelper($callback_url) {
        $this->helper = new FacebookRedirectLoginHelper($callback_url);
    }

    /**
     * @param $token
     * @throws Exception
     */
    public function setToken($token) {
        $this->token = $token;
        $this->session = new FacebookSession($this->token);

        $this->session->validate();

    }

    public function setSession($session) {
        $this->session = $session;
    }

    /**
     * @param $scope
     * @return string
     */
    public function getRedirectUrl($scope) {
        return $this->helper->getLoginUrl($scope);
    }

    /**
     * @return FacebookSession|null
     */
    public function getSessionFromRedirect() {
        return $this->helper->getSessionFromRedirect();
    }

    /**
     * @param string $method
     * @param $path
     * @param array $param
     * @return mixed
     * @throws Exception
     * @throws FacebookAuthorizationException
     * @throws FacebookClientException
     * @throws FacebookOtherException
     * @throws FacebookPermissionException
     * @throws FacebookServerException
     * @throws FacebookThrottleException
     * @throws \Facebook\FacebookRequestException
     */
    public function getResponse($method = 'GET', $path, $param = array()) {

        $request = new FacebookRequest($this->session, $method, $path, $param);
        $response = $request->execute()->getGraphObject()->asArray();

        return $response;
    }

    /**
     * @throws Exception
     */
    public function getPermission() {
        return $this->getResponse('GET', '/me/permissions', array(), null);
    }

    /**
     * @throws Exception
     */
    public function getAdminPageAccounts() {
        return $this->getResponse('GET', '/me/accounts', array(), null);
    }

    /**
     * @param $path
     * @param array $param
     * @return string
     */
    public function getPageInfo($path, $param = array()) {
        return $this->getResponse('GET', $path, $param);
    }

    /**
     * @param $path
     * @return mixed
     * @throws Exception
     * @throws FacebookAuthorizationException
     * @throws FacebookClientException
     * @throws FacebookOtherException
     * @throws FacebookPermissionException
     * @throws FacebookServerException
     * @throws FacebookThrottleException
     */
    public function getPostDetail($path) {
        return $this->getResponse('GET',$path, array());
    }

    /**
     * @param $path
     * @return mixed
     * @throws Exception
     * @throws FacebookAuthorizationException
     * @throws FacebookClientException
     * @throws FacebookOtherException
     * @throws FacebookPermissionException
     * @throws FacebookServerException
     * @throws FacebookThrottleException
     */
    public function getUserFeed($path) {
        return $this->getResponse('GET', $path, array());
    }

    public function getFullImagePost($path) {
        return $this->getResponse('GET', $path, array("fields" => "full_picture"));
    }

    /**
     * @param $requestParams
     * @return string
     */
    public function getPostsDetail($requestParams) {
        return $this->getResponse('POST', '', $requestParams);
    }

    /**
     * @return array
     */
    public function getLongAccessToken($token = null){
        $url = 'https://graph.facebook.com/oauth/access_token?client_id='.$this->appProperties['appId'].'&client_secret='.$this->appProperties['secret'].'&grant_type=fb_exchange_token&fb_exchange_token='.($token ? $token : $this->token);

        //URLからコンテンツを取得
        $url = str_replace("&amp;", "&", urldecode(trim($url)));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $contents = curl_exec($ch);

        $return = array();
        if(!$contents) return false;
        $ary = explode("&", $contents);
        if(count($ary)>0){
            foreach($ary as $item){
                list($key, $val) = explode("=", $item);
                $return[$key] = $val;
            }
        }
        return $return;
    }

    /**
     * Facebook側のキャッシュをクリアするためオブジェクトデバッガーにアクセスする
     * @param String 対象ページのURL
     * @return boolean
     */
    public function accessObjectDebugger($pageUrl) {
        if (!function_exists('curl_init')) {
            return false;
        }

        // UAを実在するもので指定する必要がある.Mac Chromeのものを使用
        $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.99 Safari/537.22';
        $debuggerUrl = 'http://developers.facebook.com/tools/debug/og/object';
        $linterUrl = sprintf('%s?q=%s', $debuggerUrl, urlencode($pageUrl));

        $curl = curl_init($linterUrl);
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_exec($curl);
        curl_close($curl);
    }

    /**
     * @param $stream
     * @return array
     */
    public function createParamForUpdateEntry($stream) {
        $entries = $this->stream_service->getEntriesForUpdateDetail($stream->id);
        if ($entries) {
            $cache_manager = new CacheManager();
            $cache_manager->deletePanelCache($stream->brand_id);
        }
        $batch_request_array = array();
        $total_entry = 0;
        $batchRequests = '';
        $paramPerRequest = '';
        foreach ($entries as $entry) {
            if ($entry->object_id && $entry->status_type != FacebookEntry::STATUS_TYPE_NOTE) {
                $total_entry += 1;
                if ($total_entry % self::PARAM_PER_CHILD_REQUEST_MAX == 0) {
                    $paramPerRequest = $this->addParamForChildRequest($paramPerRequest, $entry->object_id);
                    $batchRequests = $this->batchRequestAddRequest($batchRequests, $paramPerRequest);
                    $paramPerRequest = '';
                } else {
                    $paramPerRequest = $this->addParamForChildRequest($paramPerRequest, $entry->object_id);
                }
                if (($total_entry % (self::BATCH_REQUEST_MAX * self::PARAM_PER_CHILD_REQUEST_MAX)) == 0) {
                    $batch_request_array [$total_entry / (self::BATCH_REQUEST_MAX * self::PARAM_PER_CHILD_REQUEST_MAX) - 1] = '[' . $batchRequests . ']';
                    $batchRequests = '';
                }
            }
        }
        if ($total_entry % self::PARAM_PER_CHILD_REQUEST_MAX != 0) {
            $batchRequests = $this->batchRequestAddRequest($batchRequests, $paramPerRequest);
            $paramPerRequest = '';
        }
        if (($total_entry % (self::BATCH_REQUEST_MAX * self::PARAM_PER_CHILD_REQUEST_MAX)) != 0) {
            $batch_request_array [($total_entry - $total_entry % (self::BATCH_REQUEST_MAX * self::PARAM_PER_CHILD_REQUEST_MAX)) / (self::BATCH_REQUEST_MAX * self::PARAM_PER_CHILD_REQUEST_MAX)] = '[' . $batchRequests . ']';
            $batchRequests = '';
        }
        return $batch_request_array;
    }

    /**
     * @param $batchRequest
     * @param $param
     * @return string
     */
    private function batchRequestAddRequest($batchRequest, $param) {
        $batchRequest = $batchRequest . '{"method":"GET","relative_url":"?ids=' . $param . '"},';
        return $batchRequest;
    }

    /**
     * @param $currentParam
     * @param $extraParam
     * @return string
     */
    private function addParamForChildRequest($currentParam, $extraParam) {
        if ($currentParam == '')
            $currentParam = $extraParam;
        else
            $currentParam = $currentParam . ',' . $extraParam;
        return $currentParam;
    }

    /**
     * @param $responses
     * @param $stream
     */
    public function updateFacebookEntries($responses, $stream) {
        foreach ($responses as $response) {
            $response = (array)$response;
            $responseBody = json_decode($response['body']);
            $batch_param = '';
            foreach ($responseBody as $responseEntry) {
                $entry = $this->stream_service->getEntryByObjectID($responseEntry->id, $stream->id);

                if ($entry->detail_data_update_flg === "1" || ($entry->detail_data && $entry->detail_data != 'default')) {
                    continue;
                }
                if (($entry->type == FacebookEntry::ENTRY_TYPE_PHOTO && !$this->stream_service->getImageUrl($responseEntry)) ||
                    ($entry->type == FacebookEntry::ENTRY_TYPE_LINK && $this->stream_service->getImageUrl($responseEntry) == FacebookEntry::FACEBOOK_STAGING )) {
                    if (!$batch_param) {
                        $batch_param = '[';
                    }
                    $batch_param .= '{"method": "GET","relative_url": "/'.$entry->post_id.'?fields=full_picture"},';
                }
                try {
                    $this->stream_service->updateDetail($entry, $responseEntry, true);
                } catch (Exception $e) {
                    $this->logger->error("FacebookApiClient#updateFacebookEntries() error" . "entry_id=" . $entry->id . "stream_id=" . $stream->id);
                    $this->logger->error($e);
                }
            }

            if (!$batch_param) {
                return;
            }
            $batch_param = trim($batch_param, ',');
            $batch_param .= ']';

            $facebook_entries = aafwEntityStoreFactory::create('FacebookEntries');

            try {
                $entries_images = $this->getResponse('POST','', array('batch' => $batch_param));

                foreach ($entries_images as $entry_image) {
                    $facebook_entries->begin();

                    if ($entry_image->code != 200 || !$entry_image->body) {
                        continue;
                    }
                    $body = json_decode($entry_image->body);

                    if (!$body->full_picture) {
                        continue;
                    }

                    $entry = $this->stream_service->getEntryByPostId($body->id, $stream->id);

                    $url = $this->stream_service->uploadImage($entry, $body->full_picture);

                    if (!$url) {
                        $entry->detail_data_update_flg = 0;
                        $entry->detail_data_update_error_count += 1;
                    } else {
                        $entry->image_url = $url;
                    }

                    if($entry->detail_data_update_error_count === FacebookStreamService::DETAIL_DATA_UPDATE_MAX_ERROR_COUNT) {
                        $this->logger->error('FacebookApiClient#updateFacebookEntries() error! entry_id = ' . $entry->id. ' Update facebook entries detail error 3 times');
                        $hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
                        $hipchat_logger->error('FacebookApiClient#updateFacebookEntries() error! entry_id = ' . $entry->id. ' Update facebook entries detail error 3 times');
                    }

                    $this->stream_service->updateEntry($entry);
                    $facebook_entries->commit();
                }
            } catch (Exception $e) {
                $facebook_entries->rollback();
                $this->logger->error("FacebookApiClient#updateFacebookEntries() error" . "stream_id=" . $stream->id . "batch=" . $batch_param);
                $this->logger->error($e);
            }

        }
    }

    /**
     * @param $responses
     * @param $stream
     */
    public function updateFacebookEntriesSaveImage($responses, $stream) {
        foreach ($responses as $response) {
            $response = (array)$response;
            $responseBody = json_decode($response ['body']);
            foreach ($responseBody as $responseEntry) {
                $entry = $this->stream_service->getEntryByObjectID($responseEntry->id, $stream->id);

                if ($entry->detail_data_update_flg === "1") {
                    continue;
                }
                try {
                    $this->stream_service->updateDetail($entry, $responseEntry, true);
                } catch (Exception $e) {
                    $this->logger->error("FacebookApiClient#updateFacebookEntries() error" . "entry_id=" . $entry->id . "stream_id=" . $stream->id);
                    $this->logger->error($e);
                }
            }
        }
    }

    /**
     * @param array $param
     * @param bool $facebookBotThrough
     */
    public function fbRedirectLogin($param = array(), $facebookBotThrough = false) {
		if ($facebookBotThrough && preg_match('/^facebookexternalhit/', $_SERVER['HTTP_USER_AGENT'])) return;

        try {
            $loginUrl = $this->helper->getLoginUrl($this->scopes);
            if($param){
                foreach ($param as $key => $value) {
                    $loginUrl = $loginUrl.'&'.$key.'='.$value;
                }
            }
            self::fbRedirect($loginUrl);

        } catch(Exception $ex) {
            // When Facebook returns an error
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('FacebookApiClient#fbRedirectLogin() error');
            $logger->error($ex);
        }
	}

    /**
     * @return bool
     */
    public function checkPermissions() {

        if($this->session){
            try {
                $this->session->validate();
            } catch(FacebookSDKException $e){
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->warn('FacebookFactory#checkPermissions() FacebookSDKException');
                $logger->warn($e->getMessage());
                return false;
            }
            $fb_scopes = $this->getPermission();
            if ($fb_scopes) {
                if (is_array($fb_scopes)) {
                    foreach ($this->scopes as $scope) {
                        $isRightPermission = false;
                        foreach($fb_scopes as $fb_scope){
                            if($fb_scope->permission == $scope && $fb_scope->status == 'granted') {
                                $isRightPermission = true;
                                break;
                            }
                        }
                        if (!$isRightPermission) {
                            return false;
                        }
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    public static function fbRedirect($fbUrl) {
        echo "<script type='text/javascript'>top.location.href = '$fbUrl';</script>";
        exit();
    }

    /**
     * @param $message
     * @param $link
     * @param array $link_options
     * @return mixed
     */
    public function postShare($message, $link, $link_options = array()){
        $params = array(
            'message' => $message,
            'link' => $link
        );
        if (count($link_options)) {
            $params += $link_options;
        }
        return $this->getResponse('POST', "/me/feed", $params);
    }
}
