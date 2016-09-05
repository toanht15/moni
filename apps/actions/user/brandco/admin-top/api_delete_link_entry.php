<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.CacheManager');

class api_delete_link_entry extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_delete_link_entry';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate() {

        $this->Data['brand'] = $this->getBrand();
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_LINK, $this->Data['brand']->id);
        if (!$idValidator->isCorrectEntryId($this->entryId)) return false;

        return true;
    }

    function doAction() {


        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($this->Data['brand']->id);

        /** @var LinkEntryService $service */
        $service = $this->createService(BrandcoValidatorBase::SERVICE_NAME_LINK);

        $entry = $service->getEntryById($this->entryId);

        //パネル削除
        /** @var NormalPanelService $normal_panel_service */
        $normal_panel_service = $this->createService('NormalPanelService');
        if (!$normal_panel_service->deleteEntry($this->Data['brand'], $entry)) {
            /** @var TopPanelService $top_panel_service */
            $top_panel_service = $this->createService('TopPanelService');
            $top_panel_service->deleteEntry($this->Data['brand'], $entry);
        }

        $service->deleteEntryByBrandAndEntryId($this->Data['brand']->id, $this->entryId);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}