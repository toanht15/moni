<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_get_rss_items extends BrandcoPOSTActionBase
{
    protected $ContainerName = 'api_get_rss_items';
    protected $AllowContent = array('JSON');
    public $NeedOption = array();

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    private $streamId;
    private  $stream_service;
    private $logger;

    public function beforeValidate()
    {
    }

    public function validate()
    {
        $this->streamId = $this->GET['exts'][0];

        $this->brand = $this->getBrand();
        if(!$this->brand) {
            $json_data = $this->createAjaxResponse("ng", array(), array("message" => "other error"));
            $this->assign('json_data', $json_data);
            return false;
        }
        $idValidator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_RSS, $this->brand->id);

        if(!$idValidator->isOwner($this->streamId)) {
            $json_data = $this->createAjaxResponse("ng", array(), array("message" => "other error"));
            $this->assign('json_data', $json_data);
            return false;
        }

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->config = aafwApplicationConfig::getInstance();
        return true;
    }

    function doAction()
    {

        try {
            $this->stream_service = $this->createService("RssStreamService");
            $crawler_service = $this->createService("CrawlerService");

            $crawler_url = $crawler_service->getCrawlerUrlByTargetId("rss_stream_".$this->streamId);
            $stream = $this->stream_service->getStreamById($this->streamId);
            $rss = fetch_rss($stream->rss_url);

            $rss->image['url'] = $this->stream_service->getImageUrl($rss->image['url'],$rss->channel["link"]);
            $i = 0;
            foreach ($rss->items as $item) {
                $rss->items[$i++]['image_url'] = $this->stream_service->imageSearch($item["description"],$item["link"]);
            }

            $this->stream_service->doStore($stream, $rss, $crawler_url, 'pub_date');

        } catch (Exception $e) {
            $this->logger->error("api_get_rss_items#doAction() Exception crawler_url_id = " . $crawler_url->id);
            $this->logger->error($e);

            $json_data = $this->createAjaxResponse("ng", array(), array("message" => $e->getMessage()));
            $this->assign('json_data', $json_data);

            return 'dummy.php';
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}