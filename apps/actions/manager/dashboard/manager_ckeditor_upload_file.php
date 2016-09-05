<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
class manager_ckeditor_upload_file extends BrandcoManagerPOSTActionBase {
    protected $ContainerName = 'manager_ckeditor_upload_file';
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    function doAction() {
        $fileValidator = new FileValidator($this->FILES['upload'], FileValidator::FILE_TYPE_IMAGE);
        if ($fileValidator->isValidFile()){
            $this->Data['url'] = StorageClient::getInstance()->putObject(
                StorageClient::toHash('/free_area_entry/' . StorageClient::getUniqueId()), $fileValidator->getFileInfo()
            );
        }
        $this->Data['cback'] = $this->CKEditorFuncNum;
        return 'manager/dashboard/manager_ckeditor_upload_file.php';
    }
}
