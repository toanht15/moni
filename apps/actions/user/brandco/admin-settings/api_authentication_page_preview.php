<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.CacheManager');
class api_authentication_page_preview extends BrandcoPOSTActionBase {

    protected $ContainerName = 'api_authentication_page_preview';
    protected $AllowContent = array('JSON');

    const SESSION_TIMEOUT = 300;

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    
    public function validate() {
        return true;
    }

    function doAction() {

        try {

            $cache_manager = new CacheManager(CacheManager::PREVIEW_PREFIX);
            $cache_manager->addCacheWithTimeout(CacheManager::AUTHENTICATION_PAGE_PREVIEW_KEY, json_encode($this->POST), array($this->brand->id), self::SESSION_TIMEOUT);

        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        }

        $preview_page = Util::rewriteUrl('', 'authentication_page' , array(), array('preview' => BrandPageSettingService::AUTHENTICATION_PAGE_SESSION_PREVIEW_MODE));

        $data = array(
            'preview_url' => Util::rewriteUrl('', 'authentication_page_preview', array(), array('preview_url' => base64_encode($preview_page)))
        );

        $json_data = $this->createAjaxResponse("ok", $data);
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}
