<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_delete_file_upload extends BrandcoPOSTActionBase {
    protected $ContainerName = 'file_edit_form';
    protected $AllowContent = 'JSON';

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        $brand_upload_files = aafwEntityStoreFactory::create('BrandUploadFiles');

        try {
            $brand_upload_files->begin();

            $brand_upload_file_service = $this->createService('BrandUploadFileService');
            $upload_file_service = $this->createService('UploadFileService');
            $storage_client = StorageClient::getInstance();

            $brand_upload_file = $brand_upload_file_service->getBrandUploadFileById($this->POST['brand_upload_file_id']);
            $upload_file = $upload_file_service->getUploadFileById($brand_upload_file->file_id);
            $file_key = $storage_client->getImageKey($upload_file->url);

            $brand_upload_file_service->deleteBrandUploadFile($brand_upload_file);
            $upload_file_service->deleteUploadFile($upload_file);
            $storage_client->deleteObject($file_key);

            $json_data = $this->createAjaxResponse('ok');
            $brand_upload_files->commit();
        } catch (Exception $e) {
            $json_data = $this->createAjaxResponse('ng');
            $brand_upload_files->rollback();

            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('api_delete_file_upload@doAction Error ' . $e);
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}