<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class api_add_rss extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_add_rss';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function beforeValidate () {
    }

    public function validate () {
        if(!$this->url) {
            return false;
        }
        return true;
    }

    function doAction() {
        $brand = $this->getBrand();
        /** @var RssStreamService $service */
        $service = $this->createService("RssStreamService");

        $rss = $service->fetch_rss($this->url);
        if($rss){
            //search panel image
            $rss->image['url'] = $service->getImageUrl($rss->image['url'],$rss->channel["link"]);

            $service->createAndUpdateStreamAndCrawlerUrl($brand,$rss,$this->url);
        }
        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}