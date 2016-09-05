<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.services.PhotoUserService');
AAFW::import('jp.aainc.classes.services.CpFlowService');
AAFW::import('jp.aainc.classes.entities.PhotoUser');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class api_execute_photo_action extends ExecuteActionBase {

    public $NeedOption = array();

    protected $ContainerName = 'api_execute_photo_action';

    protected $file_info = array();

    private $logger;

    private $cp_photo_action;

    /** @var PhotoUserService $photo_user_service */
    private $photo_user_service;

    /** @var CpUserService $cp_user_service */
    private $cp_user_service;

    /** @var MultiPostSnsQueueService MultiPostSnsQueueService */
    private $multi_post_sns_queue_service;

    /** @var PhotoUserShareService $photo_user_share_service */
    private $photo_user_share_service;

    public function validate() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();

        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        // 共通validate
        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse('ng', array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        // photo_imageチェック
        $fileValidator = new FileValidator($this->FILES['photo_image'], FileValidator::FILE_TYPE_IMAGE);
        $fileValidator->isValidFile();

        if (!($fileValidator->getErrorCode() == FileValidator::ERROR_FILE_NOT_EXIST && $this->cache_photo_url)){
            if (!$fileValidator->isValidFile()) {
                $errors['invalid_upload_file'] = $fileValidator->getErrorMessage();
                $json_data = $this->createAjaxResponse("ng", array(), $errors);
                $this->assign('json_data', $json_data);
                return false;
            } else {
                $this->file_info = $fileValidator->getFileInfo();
            }
        }

        $validator_definition = array(
            'photo_title' => array(
                'required' => true,
                'type' => 'str',
                'length' => 50
            ),
            'photo_comment' => array(
                'required' => true,
                'type' => 'str',
                'length' => 300
            )
        );

        /** @var CpShippingAddressActionService $cp_shipping_address_action_service */
        $cp_photo_action_service = $this->createService('CpPhotoActionService');
        $this->cp_photo_action = $cp_photo_action_service->getCpPhotoAction($this->cp_action_id);

        if ($this->cp_photo_action->title_required != 1) {
            unset($validator_definition['photo_title']['required']);
        }
        if ($this->cp_photo_action->comment_required != 1) {
            unset($validator_definition['photo_comment']['required']);
        }

        $validator = new aafwValidator($validator_definition);
        $validator->validate($this->POST);
        if (!$validator->isValid()) {

            if ($validator->getError('photo_title')) {
                $error_messages['invalid_photo_title'] = $validator->getMessage('photo_title');
            }

            if ($validator->getError('photo_comment')) {
                $error_messages['invalid_photo_comment'] = $validator->getMessage('photo_comment');
            }

            $json_data = $this->createAjaxResponse('ng', array(), $error_messages);
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function saveData() {
        $this->photo_user_service = $this->createService('PhotoUserService');
        $this->cp_user_service = $this->getService('CpUserService');
        $this->multi_post_sns_queue_service = $this->getService('MultiPostSnsQueueService');
        $this->photo_user_share_service = $this->getService('PhotoUserShareService');

        if ($this->photo_user_service->isExistsPhotoUserByIds($this->POST)) {
            return;
        }

        // 画像アップロード
        if ($this->FILES['photo_image']) {
            $this->uploadImage();

            // Fileがある場合キャッシュURLは削除する
            if ($this->cache_photo_url) {
                $image_key = StorageClient::getInstance()->getImageKey($this->cache_photo_url);
                StorageClient::getInstance()->deleteObject($image_key);
            }

        }elseif($this->cache_photo_url) {
            $this->POST['photo_url'] = $this->cache_photo_url;
        }

        // photo_streams
        $photo_stream_service = $this->createService('PhotoStreamService');
        $photo_stream = $photo_stream_service->getStreamByBrandId($this->getBrand()->id);

        // photo_users
        $photo_user_data = $this->POST;
        if ($this->cp_photo_action->panel_hidden_flg == CpPhotoActions::APPROVE_HIDDEN_FLG_OFF) {
            $photo_user_data['approval_status'] = PhotoUser::APPROVAL_STATUS_APPROVE;
        }
        $photo_user = $this->photo_user_service->createPhotoUser($photo_user_data);

        $panel_hidden_flg = $photo_stream_service->getPhotoPanelHiddenFlg($photo_stream->panel_hidden_flg, $this->cp_photo_action->panel_hidden_flg);

        // photo_entries
        $entry = $photo_stream_service->createEmptyEntry();
        $entry->stream_id = $photo_stream->id;
        $entry->photo_user_id = $photo_user->id;
        $entry->pub_date = date('Y-m-d H:i:s');
        $entry->hidden_flg = $panel_hidden_flg;
        $photo_stream_service->updateEntry($entry);

        // cache
        if ($panel_hidden_flg == PhotoStreamService::PANEL_TYPE_AVAILABLE) {
            $cache_manager = new CacheManager();
            $cache_manager->deletePanelCache($this->getBrand()->id);

            $panel_service = $this->createService("NormalPanelService");
            $panel_service->addEntry($this->Data['brand'], $entry);

            $photo_stream_service->filterPanelByLimit($photo_stream, $photo_stream->display_panel_limit);
        }

        // photo_user_shares
        if ($this->fb_share_flg) {
            if ($this->isCanPostSNS()) {
                $this->createMultiPostSnsQueues($photo_user, SocialAccount::SOCIAL_MEDIA_FACEBOOK);
            }
            $this->createPhotoUserShare($photo_user, SocialAccountService::SOCIAL_MEDIA_FACEBOOK);
        }

        if ($this->tw_share_flg) {
            if ($this->isCanPostSNS()) {
                $this->createMultiPostSnsQueues($photo_user, SocialAccount::SOCIAL_MEDIA_TWITTER);
            }
            $this->createPhotoUserShare($photo_user, SocialAccount::SOCIAL_MEDIA_TWITTER);
        }
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
        $this->POST['photo_url'] = $storage_client->putObject($object_key, $this->file_info);

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

    /**
     * MultiPostSnsQueue作成
     * @param $photo_user
     * @param $social_media_type
     * @throws Exception
     */
    private function createMultiPostSnsQueues($photo_user, $social_media_type){
        $app_id = $this->getBrand()->app_id;
        $token_array = $this->getAccessTokenInfo($this->Data['pageStatus']['userInfo'], $social_media_type, $app_id);

        if (!count($token_array)) {
            $this->logger->error('api_execute_photo_action#getAccessTokenInfo error. photo_user_id=' . $photo_user->id);
        }

        $cp_user = $this->cp_user_service->getCpUserById($this->cp_user_id);
        $cp = CpInfoContainer::getInstance()->getCpById($cp_user->cp_id);

        $multi_post_sns_queue = $this->multi_post_sns_queue_service->createEmptyObject();
        $multi_post_sns_queue->access_token = $token_array['social_media_access_token'];
        $multi_post_sns_queue->access_refresh_token = $token_array['social_media_access_refresh_token'];
        $multi_post_sns_queue->callback_parameter = $photo_user->id;
        $multi_post_sns_queue->social_media_type = $social_media_type;
        $multi_post_sns_queue->share_text = $this->share_text;
        $multi_post_sns_queue->share_image_url = $this->POST['photo_url'];
        $multi_post_sns_queue->share_url = $photo_user->getPhotoDetailUrl($this->brand->id, $this->brand->directory_name);

        if ($this->photo_title) {
            $multi_post_sns_queue->share_title = $this->photo_title . ' - ' . $cp->getTitle();
        }else {
            $multi_post_sns_queue->share_title = $cp->getTitle();
        }

        if ($this->photo_comment) {
            $multi_post_sns_queue->share_description = $this->cutLongText($this->photo_comment, 30);
        }
        $multi_post_sns_queue->callback_function_type = MultiPostSnsQueue::CALLBACK_UPDATE_PHOTO_USER_SHARE;
        $multi_post_sns_queue->social_account_id = $this->getSNSAccountId($this->Data['pageStatus']['userInfo'], SocialAccount::$socialMediaTypeName[$social_media_type]);
        $this->multi_post_sns_queue_service->update($multi_post_sns_queue);
    }

    /**
     * モニプラからアクセストークン取得
     * @param $user_info
     * @param $social_media_type
     * @param $app_id
     * @return array
     */
    private function getAccessTokenInfo($user_info, $social_media_type, $app_id) {
        $user_sns_account_manager = new UserSnsAccountManager($user_info, null, $app_id);

        if ($social_media_type == SocialAccount::SOCIAL_MEDIA_FACEBOOK) {
            return $user_sns_account_manager->getSnsAccountInfo($social_media_type, SocialAccount::$socialMediaTypeName[SocialAccount::SOCIAL_MEDIA_FACEBOOK]);
        }elseif ($social_media_type == SocialAccount::SOCIAL_MEDIA_TWITTER){
            return $user_sns_account_manager->getSnsAccountInfo($social_media_type, SocialAccount::$socialMediaTypeName[SocialAccount::SOCIAL_MEDIA_TWITTER]);
        }
    }

    /**
     * @param $photo_user
     * @param $social_media_type
     */
    private function createPhotoUserShare($photo_user, $social_media_type){
        $photo_user_share = $this->photo_user_share_service->createEmptyObject();
        $photo_user_share->photo_user_id = $photo_user->id;
        $photo_user_share->social_media_type = $social_media_type;
        $photo_user_share->share_text = $this->share_text;
        $this->photo_user_share_service->update($photo_user_share);
    }

}
