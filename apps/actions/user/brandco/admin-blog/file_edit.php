<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class file_edit extends BrandcoPOSTActionBase {
    protected $ContainerName = 'file_edit_form';
    protected $Form = array(
        'package' => 'admin-blog',
        'action' => 'file_edit_form/{brand_upload_file_id}'
    );

    protected $ValidatorDefinition = array(
        'name' => array(
            'type' => 'str',
            'length' => 50,
            'required' => true
        )
    );

    public $NeedOption = array(BrandOptions::OPTION_CMS);
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    private $brand_upload_file_id;

    public function doThisFirst() {
        $this->brand_upload_file_id = $this->POST['brand_upload_file_id'];
    }

    public function validate() {
        return true;
    }

    public function doAction() {
        $storage_client = StorageClient::getInstance();
        $brand_upload_file_service = $this->createService('BrandUploadFileService');
        $upload_file_service = $this->createService('UploadFileService');

        $brand_upload_file = $brand_upload_file_service->getBrandUploadFileById($this->brand_upload_file_id);
        $upload_file = $brand_upload_file->getUploadFile();
        $source_key = $storage_client->getImageKey($upload_file->url);

        if ($upload_file->name != $this->POST['name']) {
            $target_key = pathinfo($source_key, PATHINFO_DIRNAME) . '/' . $this->POST['name'];
            $target_url = urldecode($storage_client->copyObject(urlencode($source_key), $target_key));

            $upload_file->name = $this->POST['name'];
            $upload_file->url = $target_url;
        }
        $upload_file->description = $this->POST['description'];

        $upload_file_service->updateUploadFile($upload_file);
        $this->Data['saved'] = 1;

        return 'redirect: ' . Util::rewriteUrl('admin-blog', 'file_edit_form', array($this->brand_upload_file_id), array('mid' => 'updated'));
    }
}