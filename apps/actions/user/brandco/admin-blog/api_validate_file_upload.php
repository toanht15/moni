<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_validate_file_upload extends BrandcoPOSTActionBase {
    protected $ContainerName = 'upload_file';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    private static $support_file_types = array(
        FileValidator::FILE_TYPE_EXCEL,
        FileValidator::FILE_TYPE_CSV,
        FileValidator::FILE_TYPE_PDF,
        FileValidator::FILE_TYPE_WORD,
        FileValidator::FILE_TYPE_POWER_POINT,
        FileValidator::FILE_TYPE_TEXT,
        FileValidator::FILE_TYPE_IMAGE,
        FileValidator::FILE_TYPE_JS,
        FileValidator::FILE_TYPE_CSS,
        FileValidator::FILE_TYPE_VIDEO,
        FileValidator::FILE_TYPE_WEB_FONT
    );

    private $file_info;

    public function validate() {
        $file_validator = new MultipleFileValidator($this->FILES['file_upload'], self::$support_file_types);
        if ($file_validator->isValidFile()) {
            $this->file_info = $file_validator->getFileInfo();
            return true;
        }

        return false;
    }

    public function doAction() {
        $upload_file_transaction = aafwEntityStoreFactory::create('UploadFiles');

        try {
            $upload_file_transaction->begin();

            $storage_client = StorageClient::getInstance();
            $upload_file_service = $this->createService('UploadFileService');

            $object_key = StorageClient::toHash('brand/' . $this->brand->id . '/upload_file/' . StorageClient::getUniqueId() . '/');
            $file_url = urldecode($storage_client->putObject($object_key . $this->file_info['name'], $this->file_info, StorageClient::ACL_PUBLIC_READ, false));

            $file_data = array();
            if ($this->file_info['file_type'] == FileValidator::FILE_TYPE_IMAGE) {
                $file_data = ImageCompositor::thumbnailImage($this->FILES['file_upload']['name']);

                if ($file_data != false) {
                    $file_info = pathinfo($this->file_info['name']);
                    $file_data['thumbnail_url'] = urldecode($storage_client->putObject($object_key . $file_info['filename'] . '_t.' . $file_info['extension'], $this->file_info, StorageClient::ACL_PUBLIC_READ, false));
                }
            }

            $user_service = $this->createService('UserService');
            $user = $user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

            $file = $upload_file_service->createEmptyUploadFile();
            $file->user_id = $user->id;
            $file->name = $this->file_info['name'];
            $file->type = $this->file_info['file_type'];
            $file->size = $this->file_info['size'];
            $file->url = $file_url;
            $file->extra_data = json_encode($file_data);
            $upload_file_service->createUploadFile($file);

            $upload_file_transaction->commit();
            $json_data = $this->createAjaxResponse('ok', array('file_id' => $file->id));
        } catch (Exception $e) {
            $upload_file_transaction->rollback();

            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('api_validate_file_upload@doAction Error: ' . $e);

            $json_data = $this->createAjaxResponse('ng');
        }
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}