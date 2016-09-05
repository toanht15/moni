<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.CacheManager');

class edit_page_entry extends BrandcoPOSTActionBase {
    protected $ContainerName = 'edit_page_entry';
    protected $Form = array (
        'package' => 'admin-top',
        'action' => 'edit_page_entry_form/{entry_id}',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    private $page_stream_service;
    private $page_entry;
    private $file_info;

    protected $ValidatorDefinition = array(
        'image_url' => array(
            'type' => 'str',
            'length' => 512,
            'validator' => array('URL')
        ),
        'panel_text' => array(
            'type' => 'str',
        ),
        'panel_image' => array (
            'type' => 'file',
            'size' => '5MB'
        )
    );

    public function validate () {
        $this->page_stream_service = $this->createService('PageStreamService');

        if($this->entry_id != 0) {
            $idValidator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_PAGE, $this->getBrand()->id);
            if (!$idValidator->isCorrectEntryId($this->entry_id)) return false;
        }

        $this->page_entry = $this->page_stream_service->getEntryById($this->entry_id);
        if (!$this->page_entry) {
            return false;
        }

        if ($this->FILES ['panel_image']) {
            $fileValidator = new FileValidator($this->FILES ['panel_image'], FileValidator::FILE_TYPE_IMAGE);
            if (!$fileValidator->isValidFile ()) {
                $this->Validator->setError('panel_image', 'NOT_MATCHES');
            } else {
                $this->file_info = $fileValidator->getFileInfo();
            }
        }

        return $this->Validator->isValid();
    }

    function doAction() {
        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($this->Data['brand']->id);

        // イメージをアップロード
        if ($this->FILES ['panel_image']) {
            $this->page_entry->image_url = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/' . $this->getBrand()->id . '/page_entry/' . StorageClient::getUniqueId()), $this->file_info
            );
        } else {
            $this->page_entry->image_url = $this->image_url;
        }
        $this->page_entry->panel_text = $this->panel_text;
        
        if(($this->page_entry->top_hidden_flg != $this->display)) {

            $this->page_entry->top_hidden_flg = $this->display;
            $this->page_entry->manual_off_flg = $this->display;

            if ($this->display == '1') {
                $this->page_entry->priority_flg = 0;
            }

            if (!$this->page_entry->isPrePublicPage()) {

                $panel_service = $this->page_entry->priority_flg ? $this->createService('TopPanelService') : $this->createService('NormalPanelService');

                if($this->display == '0') {
                    $panel_service->addEntry($this->getBrand(), $this->page_entry);
                } else {
                    $panel_service->deleteEntry($this->getBrand(), $this->page_entry);
                }
            }
        }
        $this->page_stream_service->updateEntry($this->page_entry);

        $this->Data['saved'] = 1;
        if ($this->from == 'top') {
            $return = 'redirect: ' . Util::rewriteUrl('admin-top', 'page_entries', array(), array('close' => 1, 'refreshTop' => 1));
        } else {
            $return = 'redirect: ' . Util::rewriteUrl('admin-top', 'page_entries');
        }

        return $return;
    }
}