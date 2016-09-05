<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.vendor.google.Google_Client');
AAFW::import('jp.aainc.vendor.google.contrib.Google_YouTubeService');
AAFW::import('jp.aainc.classes.CacheManager');

class api_renew_youtube_entry extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_renew_youtube_entry';
    protected $AllowContent = array('JSON');
    public $NeedOption = array();

	public $NeedAdminLogin = true;
 	public $CsrfProtect = true;
    public $logger;

	public function beforeValidate () {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
		$this->Data['service'] = $this->createService('YoutubeStreamService');
	}

	public function validate () {
        $this->brand = $this->getBrand();
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->brand->id);
        if(!$idValidator->isCorrectEntryId($this->brandSocialAccountId)) {
            $this->logger->error("api_renew_youtube_entry@validate 不正Brand");
            $json_data = $this->createAjaxResponse("ng", array(), array("message" => "other error"));
            $this->assign('json_data', $json_data);
            return false;
        }
        $idValidator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_YOUTUBE, $this->brand->id);
        if(!$idValidator->isCorrectEntryId($this->entryId)) {
            $this->logger->error("api_renew_youtube_entry@validate 不正entryId");
            $json_data = $this->createAjaxResponse("ng", array(), array("message" => "other error"));
            $this->assign('json_data', $json_data);
            return false;
        }
		return true;
	}

	function doAction()
    {
        /** @var BrandSocialAccountService $service */
        $service = $this->createService('BrandSocialAccountService');
        try {
            $cache_manager = new CacheManager();
            $cache_manager->deletePanelCache($this->brand->id);
            $brand_social_account = $service->getBrandSocialAccountById($this->brandSocialAccountId);
            $entry = $this->Data['service']->getEntryById($this->entryId);

            $client = $this->initClient();
            $client->setAccessToken($brand_social_account->token);

            $youtube = new Google_YouTubeService($client);

            $videoItem = $youtube->videos->listVideos($entry->object_id, 'snippet');
            $this->Data['service']->renewEntry($entry, $videoItem);

        }catch(Exception $e){
            $service->getErrorMessage($brand_social_account, $e);
            $this->logger->error("api_renew_youtube_entry#doAction() error" . "entry_id=" . $entry->id);
            $this->logger->error($e);
            $json_data = $this->createAjaxResponse("ng", array(), array("message" => $e->getMessage()));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
	}

    private function initClient() {
        $client = new Google_Client();
        $client->setClientId($this->config->query('@google.Google.ClientID'));
        $client->setClientSecret($this->config->query('@google.Google.ClientSecret'));
        $client->setRedirectUri(Util::getHttpProtocol().'://' . Util::getMappedServerName() . '/'.$this->config->query('@google.Google.RedirectUri'));
        $scope = array();
        $apiBase = $this->config->query('@google.Google.ApiBaseUrl');
        foreach ($this->config->query('@google.Google.Scope') as $url) {
            array_push($scope, $apiBase.'/'.$url);
        }
        $client->setScopes($scope);
        return $client;
    }
}