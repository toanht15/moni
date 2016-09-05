<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class api_delete_static_html extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_delete_static_html';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate () {
        $this->Data['brand'] = $this->getBrand();
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_STATIC_HTML, $this->Data['brand']->id);
        if(!$idValidator->isCorrectEntryId($this->entryId)) return false;
        return true;
    }

    function doAction() {
        $service = $this->createService('StaticHtmlEntryService');
        $page_stream_service = $this->createService('PageStreamService');

        $page_entry = $page_stream_service->getEntryByStaticHtmlEntryId($this->entryId);
        if ($page_entry) {
            if ($page_entry->hidden_flg == 0) {
                $cache_manager = new CacheManager();
                $cache_manager->deletePanelCache($this->getBrand()->id);

                $panel_service = $page_entry->priority_flg ? $this->createService('TopPanelService') : $this->createService('NormalPanelService');
                $panel_service->deleteEntry($this->getBrand(), $page_entry);
            }

            $page_stream_service->deleteEntry($page_entry);
        }

        // Disable comment plugin
        $comment_plugin_service = $this->createService('CommentPluginService');
        $comment_plugin = $comment_plugin_service->getCommentPlugin($this->getBrand()->id, $this->entryId);
        if (!Util::isNullOrEmpty($comment_plugin) && $comment_plugin->isPublic()) {
            $comment_plugin->status = CommentPlugin::COMMENT_PLUGIN_STATUS_PRIVATE;
            $comment_plugin_service->updateCommentPlugin($comment_plugin);
        }

        $service->deleteEntry($this->Data['brand']->id,$this->entryId);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}