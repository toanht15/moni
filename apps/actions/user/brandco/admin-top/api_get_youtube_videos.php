<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
require_once('vendor/google/Google_Client.php');
require_once('vendor/google/contrib/Google_YouTubeService.php');
require_once('vendor/google/contrib/Google_Oauth2Service.php');

class api_get_youtube_videos extends BrandcoPOSTActionBase
{
    protected $ContainerName = 'api_get_youtube_videos';
    protected $AllowContent = array('JSON');
    public $NeedOption = array();

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    private $brandSocialAccountId;

    public function validate()
    {

        $this->brandSocialAccountId = $this->GET['exts'][0];

        $this->brand = $this->getBrand();
        if(!$this->brand) {
            aafwLog4phpLogger::getDefaultLogger()->error("api_get_youtube_videos@validate 不正Brand");
            $json_data = $this->createAjaxResponse("ng", array(), array("message" => "other error"));
            $this->assign('json_data', $json_data);
            return false;
        }
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->brand->id);
        if(!$idValidator->isCorrectEntryId($this->brandSocialAccountId)) {
            aafwLog4phpLogger::getDefaultLogger()->error("api_get_youtube_videos@validate 不正brandSocialAccountId");
            $json_data = $this->createAjaxResponse("ng", array(), array("message" => "other error"));
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function doAction()
    {
        /** @var BrandSocialAccountService $brandService */
        $brandService = $this->createService('BrandSocialAccountService');

        try {
            $crawler_service = $this->createService("CrawlerService");
            /** @var YoutubeStreamService $stream_service */
            $stream_service = $this->createService("YoutubeStreamService");

            $brand_social_account = $brandService->getBrandSocialAccountById($this->brandSocialAccountId);
            $stream = $brand_social_account->getYoutubeStream();

            $crawler_url = $crawler_service->getCrawlerUrlByTargetId("youtube_stream_".$stream->id);

            $client = $this->initClient();
            $client->setAccessToken($brand_social_account->token);

            $youtube = new Google_YouTubeService($client);

            $channelsResponse = $youtube->channels->listChannels('contentDetails', array(
                'mine' => 'true',
            ));

            foreach ($channelsResponse['items'] as $channel) {
                $playlistItems = $stream_service->getYoutubeVideoInfo($channel, $youtube);
                $stream_service->doStore($stream, $crawler_url, $playlistItems, 'pub_date');
            }

        } catch (Exception $e) {
            $brandService->getErrorMessage($brand_social_account, $e);
            aafwLog4phpLogger::getDefaultLogger()->error("api_get_youtube_videos#doAction() Exception crawler_url_id = " . $crawler_url->id);
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            $json_data = $this->createAjaxResponse("ng", array(), array("message" => $e->getMessage()));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }
        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }

    /**
     * @return Google_Client
     */
    private function initClient() {
        $client = new Google_Client();
        $client->setClientId($this->config->query('@google.Google.ClientID'));
        $client->setClientSecret($this->config->query('@google.Google.ClientSecret'));
        $client->setRedirectUri(Util::getHttpProtocol().'://'. Util::getMappedServerName() . '/'.$this->config->query('@google.Google.RedirectUri'));
        $scope = array();
        $apiBase = $this->config->query('@google.Google.ApiBaseUrl');
        foreach ($this->config->query('@google.Google.Scope') as $url) {
            array_push($scope, $apiBase.'/'.$url);
        }
        $client->setScopes($scope);
        return $client;
    }

}