<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import ( 'jp.aainc.aafw.file.aafwFileManager' );

class api_write_tmp extends BrandcoGETActionBase {

    protected $AllowContent = array('JSON');

    const PAGE_PREVIEW              = 1;
    const CATEGORY_PREVIEW          = 2;
    const COMMENT_PLUGIN_PREVIEW    = 3;

    const SESSION_TIMEOUT = 300;

    public $NeedOption = array();
    private $preview_links = array(
        self::PAGE_PREVIEW              => 'page',
        self::CATEGORY_PREVIEW          => 'categories',
        self::COMMENT_PLUGIN_PREVIEW    => 'comment_plugin'
    );

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

    public function validate() {
        return true;
    }

    function doAction() {
        try {
            $cache_manager = new CacheManager(CacheManager::PREVIEW_PREFIX);
            $cache_manager->addCacheWithTimeout($this->getPreviewKey(), json_encode($this->POST), array($this->getBrand()->id), self::SESSION_TIMEOUT);
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        }

        $data = array(
            'preview_url' => $this->getPreviewUrl()
        );

        $json_data = $this->createAjaxResponse("ok", $data);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    /**
     * Fetching preview page
     * @return string
     */
    public function getPreviewUrl() {
        if ($this->POST['preview_type'] == self::COMMENT_PLUGIN_PREVIEW) {
            return Util::rewriteUrl('plugin', 'embed');
        }

        $preview_page = Util::rewriteUrl('', $this->preview_links[$this->POST['preview_type']], array(), array('preview' => StaticHtmlEntries::SESSION_PREVIEW_MODE));
        return Util::rewriteUrl('', 'preview', array(), array('preview_url' => base64_encode($preview_page)));
    }

    /**
     * Fetching preview key
     * @return string
     */
    public function getPreviewKey() {
        if (array_key_exists('customize_code', $this->POST)) {
            return CacheManager::CATEGORIES_PREVIEW_KEY;
        }

        if ($this->POST['preview_type'] == self::COMMENT_PLUGIN_PREVIEW) {
            return CacheManager::COMMENT_PLUGIN_PREVIEW_KEY;
        }

        return CacheManager::PAGE_PREVIEW_KEY;
    }
}
