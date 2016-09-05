<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class api_sns_connect_cache extends BrandcoPOSTActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_sns_connect_cache';
    public $NeedUserLogin = true;
    public $CsrfProtect = true;
    protected $AllowContent = array('JSON');
    protected $file_info;

    function validate() {

        // photo_imageチェック
        if ($this->FILES['photo_image']) {
            $fileValidator = new FileValidator($this->FILES['photo_image'], FileValidator::FILE_TYPE_IMAGE);
            if (!$fileValidator->isValidFile()) {
                $errors['invalid_upload_file'] = $fileValidator->getErrorMessage();
                $json_data = $this->createAjaxResponse("ng", array(), $errors);
                $this->assign('json_data', $json_data);
                return false;
            } else {
                $this->file_info = $fileValidator->getFileInfo();
            }
        }

        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function doAction() {

        // 画像アップロード
        if ($this->FILES['photo_image']) {
            $this->uploadImage();
        }

        $cache_data = array();

        // キャッシュ保存データ整形
        if ($this->photo_title) {
            $cache_data['photo_title'] = $this->photo_title;
        }

        if ($this->photo_comment) {
            $cache_data['photo_comment'] = $this->photo_comment;
        }

        if ($this->photo_url) {
            $cache_data['photo_url'] = $this->photo_url;
        }

        if ($this->share_text) {
            $cache_data['share_text'] = $this->share_text;
        }

        try {

            $cache_manager = new CacheManager();

            // キャッシュが他のユーザにでてしまう最悪のケース防止
            $error_cache_data = $cache_manager->getCache('phau');
            if ($error_cache_data) {
                $cache_manager->deleteCache($error_cache_data);
            }

            if ($this->cp_action_id && $this->cp_user_id) {
                $cache_id = 'ph' . 'a' . $this->cp_action_id . 'u' . $this->cp_user_id;
                // cacheに保存
                $cache_manager->deleteCache($cache_id);
                $cache_manager->addCache($cache_id , json_encode($cache_data));
            }

            $return_data = array();
            $return_data['connect_url'] = $this->connect_url;
            $return_data['connect_url'] .= '?redirect_url=' . urlencode($this->redirect_url);
            $return_data['connect_url'] .= '&cp_id=' . $this->cp_id;
            $return_data['connect_url'] .= '&platform=' . $this->platform;

            $json_data = $this->createAjaxResponse('ok', $return_data);
            $this->assign('json_data', $json_data);

        }catch(Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('api_sns_connect_cache#doAction cache error.');
            $logger->error($e);

            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }
        return 'dummy.php';
    }

    /**
     * @throws \Aws\S3\Exception\S3Exception
     */
    public function uploadImage() {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $cp_action = CpInfoContainer::getInstance()->getCpActionById($this->cp_action_id);
        $cp = $cp_flow_service->getCpByCpAction($cp_action);

        $object_key = StorageClient::toHash('brand/' . $this->brand->id . '/cp_action_photo/' . $cp->id . '/user/' . $this->cp_user_id . '/' . StorageClient::getUniqueId());
        $storage_client = StorageClient::getInstance();

        // Origin image
        $this->photo_url = $storage_client->putObject($object_key, $this->file_info);

        // Scale image
        $clone_img_m = ImageCompositor::cloneImage($this->FILES['photo_image']['name'], 'm');
        $file_validator_m = new FileValidator($clone_img_m, FileValidator::FILE_TYPE_IMAGE);
        $file_validator_m->isValidFile();
        ImageCompositor::scaleImageWidth($clone_img_m['name'], 520);
        $storage_client->putObject($object_key . '_m', $file_validator_m->getFileInfo());

        // Cropped image
        $clone_img_s = ImageCompositor::cloneImage($this->FILES['photo_image']['name'], 's');
        $file_validator_s = new FileValidator($clone_img_s, FileValidator::FILE_TYPE_IMAGE);
        $file_validator_s->isValidFile();
        ImageCompositor::cropSquareImage($clone_img_s['name']);
        $storage_client->putObject($object_key . '_s', $file_validator_s->getFileInfo());
    }
}
