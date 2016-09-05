<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class ckeditor_upload_file extends BrandcoPOSTActionBase {
    protected $ContainerName = 'ckeditor_upload_file';

    public $NeedAdminLogin = true;
    
    public $NeedOption = array();

	public function validate() {
		return true;
	}

	function doAction() {
		$brand = $this->getBrand();

		$fileValidator = new FileValidator($this->FILES['upload'], FileValidator::FILE_TYPE_IMAGE);
		if ($fileValidator->isValidFile()){
            $this->Data['url'] = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/' . $brand->id . '/free_area_entry/' . StorageClient::getUniqueId()), $fileValidator->getFileInfo()
            );
		}
		$this->Data['cback'] = $this->CKEditorFuncNum;
		return 'user/brandco/admin-top/ckeditor_upload_file.php';
	}
}
