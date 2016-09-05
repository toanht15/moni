<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.CacheManager');
class edit_link_entry extends BrandcoPOSTActionBase {
	protected $ContainerName = 'edit_link_entry';
	protected $Form = array (
		'package' => 'admin-top',
		'action' => 'edit_link_entry_form/{entryId}',
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
		'title' => array(
			'required' => true,
			'type' => 'str',
			'length' => 35,
		),
		'image_url' => array(
			'type' => 'str',
			'length' => 512,
			'validator' => array('URL')
		),
		'body' => array(
				'type' => 'str',
        ),
		'panel_image' => array (
				'type' => 'file',
				'size' => '5MB'
		)
	);

	public function beforeValidate () {
	}

	public function validate () {
		$this->Data['link_entry_service'] = $this->createService('LinkEntryService');
		$this->Data['brand'] = $this->getBrand();

        if($this->entryId != 0) {
            $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_LINK, $this->Data['brand']->id);
            if (!$idValidator->isCorrectEntryId($this->entryId)) return false;
        }

		//check page url is existed
		$entry = $this->Data['link_entry_service']->getEntryByBrandIdAndPageUrl($this->Data['brand']->id,$this->link);

		if($entry){
			if(($this->entryId && ($this->entryId != $entry->id)) || !$this->entryId){
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

		$link_entry_service = $this->createService('LinkEntryService');
		$brand = $this->getBrand();
		$linkEntry = $link_entry_service->getEntryByBrandIdAndEntryId($brand->id, $this->entryId);
		if(!$linkEntry) {
			$linkEntry = $link_entry_service->createEmptyEntry();
			$linkEntry->brand_id = $brand->id;
			$linkEntry->hidden_flg = $this->display;
			$linkEntry->created_at = date('Y-m-d H:i:s');
			$linkEntry->pub_date = date('Y-m-d H:i:s');
		}

		if(($linkEntry->hidden_flg != $this->display) && $this->entryId != 0) {
			if($linkEntry->priority_flg){
				$panel_service = $this->createService('TopPanelService');
			}else{
				$panel_service = $this->createService('NormalPanelService');
			}
			if($this->display == '0'){
				$panel_service->addEntry($this->Data['brand'],$linkEntry);
			}else{
				$panel_service->deleteEntry($this->Data['brand'],$linkEntry);
			}
		}

		$linkEntry->title = $this->title;
		$linkEntry->link = $this->link;
		$linkEntry->image_url = $this->image_url;
		$linkEntry->body = $this->body;
        $linkEntry->target = $this->target ? $this->target : 0;
		$linkEntry->del_flg = 0;
		$linkEntry->updated_at = date('Y-m-d H:i:s');

		// イメージをアップロード
		if ($this->FILES ['panel_image']) {
            $linkEntry->image_url = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/' . $brand->id . '/link_entry/' . StorageClient::getUniqueId()), $this->file_info
            );
		}else{
			$linkEntry->image_url = $this->image_url;
		}
		$linkEntry = $link_entry_service->createEntry($linkEntry);

		// add to normal panel
		if($this->entryId == 0 && $linkEntry->hidden_flg == 0){
			$normal_panel_service = $this->createService('NormalPanelService');
			$normal_panel_service->addEntry($brand,$linkEntry);
		}

		$this->entryId = $linkEntry->id;

		if($this->Validator->getErrorCount()) {
			$return = $this->getFormURL();
		} else{
            $this->Data['saved'] = 1;

			if($this->from == 'top'){
				$return = 'redirect: ' . Util::rewriteUrl ( 'admin-top', 'link_entries', array(),array('close' =>1,'refreshTop'=>1));
			}else{
				$return = 'redirect: '.Util::rewriteUrl('admin-top', 'link_entries');
			}
		}
		return $return;
	}
}