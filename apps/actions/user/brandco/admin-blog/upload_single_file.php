<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class upload_single_file extends BrandcoPOSTActionBase {
    protected $ContainerName = 'upload_file';
    protected $Form = array(
        'package' => 'admin-blog',
        'action' => 'file_list?mid=failed'
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    private $file_info;

    private static $support_file_types = array(
        FileValidator::FILE_TYPE_EXCEL,
        FileValidator::FILE_TYPE_CSV,
        FileValidator::FILE_TYPE_PDF,
        FileValidator::FILE_TYPE_WORD,
        FileValidator::FILE_TYPE_POWER_POINT,
        FileValidator::FILE_TYPE_TEXT,
        FileValidator::FILE_TYPE_IMAGE,
        FileValidator::FILE_TYPE_JS,
        FileValidator::FILE_TYPE_CSS
    );

    protected $ValidatorDefinition = array(
        'file_upload' => array (
            'type' => 'file',
            'size' => '10MB'
        )
    );

    public function validate() {
        $file_validator = new MultipleFileValidator($this->FILES['file_upload'], self::$support_file_types);
        if ($file_validator->isValidFile()) {
            $this->file_info = $file_validator->getFileInfo();
        } else {
            $this->Validator->setError('file_upload', 'NOT_MATCHES');
        }

        return $this->Validator->isValid();
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

            $upload_file = $upload_file_service->createEmptyUploadFile();
            $upload_file->user_id = $user->id;
            $upload_file->name = $this->file_info['name'];
            $upload_file->type = $this->file_info['file_type'];
            $upload_file->size = $this->file_info['size'];
            $upload_file->url = $file_url;
            $upload_file->extra_data = json_encode($file_data);
            $upload_file->hidden_flg = 0;
            $upload_file_service->createUploadFile($upload_file);

            $brand_upload_file_service = $this->createService('BrandUploadFileService');

            $brand_upload_file = $brand_upload_file_service->createEmptyBrandUploadFile();
            $brand_upload_file->brand_id = $this->getBrand()->id;
            $brand_upload_file->file_id = $upload_file->id;
            $brand_upload_file->pub_date = date('Y-m-d H:i:s');
            $brand_upload_file_service->createBrandUploadFile($brand_upload_file);

            $upload_file_transaction->commit();

            $this->Data['saved'] = 1;
            $redirect_url = Util::rewriteUrl('admin-blog', 'file_list', array(), array('mid' => 'action-created'));
        } catch (Exception $e) {
            $upload_file_transaction->rollback();

            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('upload_single_file@doAction Error: ' . $e);

            $redirect_url = Util::rewriteUrl('admin-blog', 'file_list', array(), array('mid' => 'failed'));
        }

        return 'redirect: ' . $redirect_url;
    }
}