<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.CacheManager');

class edit_photo_entry extends BrandcoPOSTActionBase {
    protected $ContainerName = 'edit_photo_entry';
    protected $Form = array(
        'package' => 'admin-top',
        'action' => 'edit_photo_entry_form/{entry_id}',
    );

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    public $NeedOption = array();

    public function validate() {
        $this->Data['brand'] = $this->getBrand();

        $id_validator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_PHOTO, $this->Data['brand']->id);
        return $id_validator->isCorrectEntryId($this->entry_id);
    }

    function doAction() {
        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($this->Data['brand']->id);

        $photo_stream_servivce = $this->createService('PhotoStreamService');
        $photo_entry = $photo_stream_servivce->getEntryById($this->entry_id);

        if (($photo_entry->hidden_flg != $this->display)) {
            if ($photo_entry->priority_flg) {
                $panel_service = $this->createService('TopPanelService');
            } else {
                $panel_service = $this->createService('NormalPanelService');
            }
            if ($this->display == '0') {
                $panel_service->addEntry($this->Data['brand'], $photo_entry);
            } else {
                $panel_service->deleteEntry($this->Data['brand'], $photo_entry);
            }
        }

        $this->Data['saved'] = 1;
        if ($this->from == 'top') {
            $return = 'redirect: ' . Util::rewriteUrl('admin-top', 'photo_entries', array(), array('close' => 1, 'refreshTop' => 1));
        } else {
            $return = 'redirect: ' . Util::rewriteUrl('admin-top', 'photo_entries');
        }

        return $return;
    }
}