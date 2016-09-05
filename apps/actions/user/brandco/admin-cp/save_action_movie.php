<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');
AAFW::import('jp.aainc.classes.entities.CpMovieAction');

class save_action_movie extends SaveActionBase {
    protected $ContainerName = 'save_action_movie';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    protected $brand = null;
    protected $file_info = array();

    protected $ValidatorDefinition = array(
        'title' => array(
            'required' => true,
            'type' => 'str',
            'length' => 50
        ),
        'image_url' => array(
            'type' => 'str',
            'length' => 512,
            'validator' => array('URL')
        ),
        'video_url' => array(
            'type' => 'str',
            'length' => 512,
            'validator' => array('URL')
        ),
        'image_file' => array(
            'type' => 'file',
            'size' => '5MB'
        ),
        'video_file' => array(
            'type' => 'file',
            'size' => '100MB'
        ),
        'text' => array(
            'type' => 'str',
            'length' => CpValidator::MAX_TEXT_LENGTH
        ),
        'movie_object_id_url' => array(
            'type' => 'str',
            'length' => 11
        ),
    );

    public function doThisFirst() {
        ini_set('memory_limit', '500M');
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['moduleImage']['required'] = true;
            $this->ValidatorDefinition['title']['required'] = true;
            if( $this->POST['module_movie'] == CpMovieAction::IS_YOUTUBE_ID ) {
                $this->ValidatorDefinition['movie_object_id_url']['required'] = true;
            }
            if($this->POST['module_movie'] == CpMovieAction::IS_UPLOADED){
                if($this->POST['upload_movie'] == CpMovieAction::IS_UPLOADED_URL){
                    $this->ValidatorDefinition['movie_upload_url']['required'] = true;
                } elseif ($this->POST['upload_movie'] == CpMovieAction::IS_UPLOADED_FILE) {
                    // $this->ValidatorDefinition['video_file']['required'] = true;
                }
            }
            
        }

        $this->fetchDeadLineValidator();
    }

    public function validate() {
        $this->brand = $this->getBrand();
        $validatorService = new CpValidator($this->brand->id);

        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }

        if ($this->FILES['image_file']) {
            $fileValidator = new FileValidator($this->FILES['image_file'], FileValidator::FILE_TYPE_IMAGE);
            if (!$fileValidator->isValidFile()) {
                $this->Validator->setError('image_file', 'NOT_MATCHES');
                return false;
            } else {
                $this->file_info = $fileValidator->getFileInfo();
            }
        }
        if($this->POST['module_movie'] == CpMovieAction::IS_UPLOADED && $this->POST['upload_movie'] == CpMovieAction::IS_UPLOADED_FILE){
            $fileValidator = new FileValidator($this->FILES['video_file'], FileValidator::FILE_TYPE_VIDEO);
            if (!$fileValidator->isValidFile()) {
                $this->Validator->setError('video_file', 'NOT_MATCHES');
                return false;
            } else {
                $this->file_info = $fileValidator->getFileInfo();
            }
        }

        if (!$this->validateDeadLine()) return false;

        return true;
    }

    function doAction() {
        $upload_file_transaction = aafwEntityStoreFactory::create('UploadFiles');
        try{
            $upload_file_transaction->begin();
            $this->getCpAction()->status = $this->POST['save_type'];

            $data = array();

            $data['title'] = $this->POST['title'];
            $data['text'] = $this->POST['text'];

            if ($this->FILES['image_file']) {
                // メインバナー画像 保存
                $data['image_url'] = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info, "cp_action_movie");
            } else {
                $data['image_url'] = $this->POST['image_url'];
            }

            if($this->POST['module_movie'] == CpMovieAction::IS_UPLOADED) {
                if($this->POST['upload_movie'] == CpMovieAction::IS_UPLOADED_FILE){
                    $data['movie_url'] = StorageClient::getInstance()->putObject(
                        StorageClient::toHash('brand/' . $this->Data['brand']->id . '/cp_action_movie/' . StorageClient::getUniqueId()), $this->file_info
                    );
                    // <-----save into file list---->
                    $user_service = $this->createService('UserService');
                    $user = $user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

                    $upload_file_service = $this->createService('UploadFileService');
                    $file = $upload_file_service->createEmptyUploadFile();
                    $file->user_id = $user->id;
                    $file->name = $this->file_info['name'];
                    $file->type = $this->file_info['file_type'];
                    $file->size = $this->file_info['size'];
                    $file->url = $data['movie_url'];
                    $upload_file_service->createUploadFile($file);

                    $brand_upload_file_service = $this->createService('BrandUploadFileService');
                    $brand_upload_file = $brand_upload_file_service->createEmptyBrandUploadFile();
                    $brand_upload_file->brand_id = $this->getBrand()->id;
                    $brand_upload_file->file_id = $file->id;
                    $brand_upload_file->pub_date = date('Y-m-d H:i:s');
                    $brand_upload_file_service->createBrandUploadFile($brand_upload_file);
                    // <----- end ------->
                }else{
                    $data['movie_url'] = $this->POST['movie_upload_url'];
                }
            } else {
                if ($this->POST['moduleMovie'] == 1) {
                    $data['movie_object_id'] = $this->POST['movie_object_id_select'];
                } else {
                    $data['movie_object_id'] = $this->POST['movie_object_id_url'];
                }
                
            }
            $data['popup_view_flg'] = $this->POST['popup_view_flg']?:0;
            $data['movie_type'] = $this->POST['module_movie'];
            $this->renewDeadLineData();
            $this->getActionManager()->updateCpActions($this->getCpAction(), $data);

            $upload_file_transaction->commit();

            $this->Data['saved'] = 1;

            if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
                $this->POST['callback'] = $this->POST['callback'] . '?mid=action-saved';
            } else {
                $this->POST['callback'] = $this->POST['callback'] . '?mid=action-draft';
            }
        } catch(Exception $e) {
            $upload_file_transaction->rollback();

            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('save_action_movie@doAction Error: ' . $e);

            $this->POST['callback'] = $this->POST['callback'] . '?mid=action-false';

        }
        return 'redirect: ' . $this->POST['callback'];
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpMovieActionManager');
    }
}
