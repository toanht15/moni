<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_free_area_preview extends BrandcoPOSTActionBase {

    protected $ContainerName = 'api_free_area_preview';
    protected $AllowContent = array('JSON');
    public $NeedOption = array();

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

    public function validate() {
        return true;
    }

    function doAction() {
        try {
            $cache_manager = new CacheManager(CacheManager::PREVIEW_PREFIX);
            $cache_manager->addCacheWithTimeout(CacheManager::FREE_AREA_PREVIEW_KEY, json_encode($this->body), array($this->brand->id));

        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
