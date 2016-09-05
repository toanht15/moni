<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class file_edit_form extends BrandcoGETActionBase {
    protected $ContainerName = 'file_edit_form';

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    private $brand_upload_file_id;

    public function doThisFirst() {
        $this->deleteErrorSession();
        $this->brand_upload_file_id = $this->GET['exts'][0];
    }

    public function validate() {
        $brand_upload_file_service = $this->createService('BrandUploadFileService');
        $this->Data['brand_upload_file'] = $brand_upload_file_service->getBrandUploadFileById($this->brand_upload_file_id);

        if ($this->Data['brand_upload_file']->brand_id != $this->getBrand()->id) {
            return false;
        }

        $this->Data['upload_file'] = $this->Data['brand_upload_file']->getUploadFile();
        return true;
    }

    public function doAction() {
        $this->assign('ActionForm', $this->Data['upload_file']->toArray());

        return 'user/brandco/admin-blog/file_edit_form.php';
    }
}