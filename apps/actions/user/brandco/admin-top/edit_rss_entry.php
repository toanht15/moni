<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.CacheManager');
class edit_rss_entry extends BrandcoPOSTActionBase {
	protected $ContainerName = 'edit_rss_entry';
	protected $Form = array (
		'package' => 'admin-top',
		'action' => 'edit_rss_entry_form/{entryId}',
	);

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	public $CsrfProtect = true;
    private $file_info = array();

	protected $ValidatorDefinition = array(
		'link' => array(
            'required' => true,
			'type' => 'str',
			'length' => 255,
			'validator' => array('URL')
		),
		'image_url' => array(
			'type' => 'str',
			'length' => 512,
			'validator' => array('URL')
		),
		'panel_text' => array(
				'type' => 'str',
                'length' => 300
        ),
		'panel_image' => array (
				'type' => 'file',
				'size' => '5MB'
		)
	);

	public function beforeValidate () {
	}

	public function validate () {
        $this->Data['brand'] = $this->getBrand();
		$this->Data['service'] = $this->createService('RssStreamService');

        $idValidator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_RSS,$this->Data['brand']->id);
        if(!$idValidator->isCorrectEntryId($this->entryId)) return false;

		//check page url is existed
		$entry = $this->Data['service']->getEntryByStreamIdAndPageUrl($this->streamId,$this->link);

		if($entry){
			if($this->entryId && ($this->entryId != $entry->id)){
				$this->Validator->setError('link','EXISTED_PAGE_URL');
			}
		}

		if ($this->FILES ['panel_image']) {
			$fileValidator = new FileValidator ( $this->FILES ['panel_image'],FileValidator::FILE_TYPE_IMAGE );
			if (!$fileValidator->isValidFile ()) {
				$this->Validator->setError('panel_image', 'NOT_MATCHES');
			}else{
                $this->file_info = $fileValidator->getFileInfo();
            }
		}

		if($this->Validator->getErrorCount()) return false;
		return true;
	}

	function doAction() {

        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($this->Data['brand']->id);

		$entry = $this->Data['service']->getEntryById($this->entryId);
		if(!$entry || $this->entryId == 0) {
			return '403';
		}

		if($entry->hidden_flg != $this->display) {
			if($entry->priority_flg){
				$panel_service = $this->createService('TopPanelService');
			}else{
				$panel_service = $this->createService('NormalPanelService');
			}
			if($this->display == '0'){
				$panel_service->addEntry($this->Data['brand'],$entry);
			}else{
				$panel_service->deleteEntry($this->Data['brand'],$entry);
			}
		}

        $entry->panel_text = $this->panel_text;
        $entry->link = $this->link;
        $entry->del_flg = 0;
        $entry->updated_at = date('Y-m-d H:i:s');

		// イメージをアップロード
		if ($this->FILES ['panel_image']) {
            $entry->image_url = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/' . $this->Data['brand']->id . '/rss_entry/' . StorageClient::getUniqueId()), $this->file_info
            );
		}else{
            $entry->image_url = $this->image_url;
		}
        $entry = $this->Data['service']->updateEntry($entry);

		$this->resetActionContainerByName();

        if($this->from == 'top'){
            $return = 'redirect: ' . Util::rewriteUrl ( 'admin-top', 'rss_entries', array($this->streamId),array('close' =>1,'refreshTop'=>1));
        }else{
            $return = 'redirect: '.Util::rewriteUrl('admin-top', 'rss_entries', array($this->streamId));
        }

		return $return;
	}
}