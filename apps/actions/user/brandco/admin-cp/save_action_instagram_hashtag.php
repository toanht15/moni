<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');
AAFW::import('jp.aainc.vendor.instagram.Instagram');

class save_action_instagram_hashtag extends SaveActionBase {

    protected $ContainerName = 'save_action_instagram_hashtag';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );
    /** @var Instagram $instagram */
    protected $instagram;

    private $cp_instagram_hashtag;
    private $image_url;
    private $logger;
    /** @var CpInstagramHashtagService $cp_instagram_hashtag_service */
    private $cp_instagram_hashtag_service;
    /** @var CpInstagramHashtagActionService $cp_instagram_hashtag_action_service */
    private $cp_instagram_hashtag_action_service;

    private $hash_transaction;

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['text']['required'] = true;
            $this->ValidatorDefinition['moduleImage']['required'] = true;
            $this->ValidatorDefinition['title']['required'] = true;
            $this->ValidatorDefinition['button_label_text']['required'] = true;
            $this->ValidatorDefinition['hashtags']['required'] = true;
            $this->ValidatorDefinition['approval_flg']['required'] = true;
        }

        $this->fetchDeadLineValidator();
    }

    protected $ValidatorDefinition = array(
        'title' => array('type' => 'str','length' => 50),
        'image_url' => array('type' => 'str','length' => 512,'validator' => array('URL')),
        'image_file' => array('type' => 'file','size' => '5MB'),
        'text' => array('type' => 'str','length' => CpValidator::MAX_TEXT_LENGTH),
        'button_label_text' => array('type' => 'str','length' => 80),
        'skip_flg' => array('type' => 'str'),
        'autoload_flg' => array('type' => 'str'),
        'hashtags' => array('type' => 'str', 'validator' => 'isEmpty'),
        'approval_flg' => array('type' => 'str')
    );

    public function validate() {

        $this->brand = $this->getBrand();
        $validatorService = new CpValidator($this->brand->id);

        if (!$validatorService->isOwnerOfAction($this->action_id)) {
            $this->Validator->setError('auth', 'NOT_OWNER');
        }

        if ($this->FILES['image_file']) {
            $fileValidator = new FileValidator($this->FILES['image_file'], FileValidator::FILE_TYPE_IMAGE);
            if (!$fileValidator->isValidFile()) {
                $this->Validator->setError('image_file', 'NOT_MATCHES');
            } else {
                $this->file_info = $fileValidator->getFileInfo();
            }
        }

        if (count($this->POST['hashtags'])) {
            // 重複チェック
            if ($this->POST['hashtags'] !== array_unique($this->POST['hashtags'])) {
                $this->Validator->setError('hashtags', 'NOT_SAME_HASHTAG');
            }elseif (count($this->POST['hashtags']) > CpInstagramHashtag::MAX_HASHTAG_COUNT) {
                $this->Validator->setError('hashtags', 'OVER_HASHTAG');
            }

            foreach ($this->POST['hashtags'] as $hashtag) {
                if (preg_match('#\~|\%|\:|\/|\?|\#|\[|\]|\@|\!|\$|\&|\(|\)|\*|\+|\,| |　|×#', $hashtag)) {
                    $this->Validator->setError('hashtags', 'INCLUDE_SPECIALCHAR');
                }elseif(mb_strlen($hashtag, 'utf-8') > CpInstagramHashtag::MAX_HAHTAAG_LENGTH) {
                    $this->Validator->setError('hashtags', 'HAHTAG_LENGTH_OVER');
                }
            }
        }

        if (!$this->validateDeadLine()) return false;

        return !$this->Validator->getErrorCount();
    }

    public function afterValidate() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->cp_instagram_hashtag_service = $this->getService('CpInstagramHashtagService');
        $this->cp_instagram_hashtag_action_service = $this->getService('CpInstagramHashtagActionService');
        $this->hash_transaction = aafwEntityStoreFactory::create('CpInstagramHashtagActions');
    }

    function doAction() {

        try {
            $this->hash_transaction->begin();

            // 画像設定
            $this->uploadImage();

            // concrete_actionをfill
            $this->fillCpInstagramHashtagAction();

            $this->getCpAction()->status = $this->save_type;
            $this->renewDeadLineData();

            // cp_action と cp_concrete_action設定
            $this->getActionManager()->updateCpActions($this->getCpAction(), $this->cp_instagram_hashtag);

            // hashtag更新
            $this->cp_instagram_hashtag_service->refreshCpInstagramHashtagsByCpActionIdAndHashtags($this->getConcreteAction()->id, $this->POST['hashtags']);

            if ($this->POST['save_type'] == CpAction::STATUS_FIX && $this->cp->type == Cp::TYPE_MESSAGE) {
                $this->cp_instagram_hashtag_action_service->initializeInstagramHashtagByCpId($this->cp->id);
            }

            $this->hash_transaction->commit();

        }catch(Exception $e) {
            $this->logger->error($e);
            $this->logger->error($e->getMessage());
            $this->hash_transaction->rollback();
            return 'redirect: ' . $this->POST['callback'] . '?mid=failed';
        }

        $this->Data['saved'] = 1;

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->POST['callback'] = $this->POST['callback'] . '?mid=action-saved';
        } else {
            $this->POST['callback'] = $this->POST['callback'] . '?mid=action-draft';
        }

        return 'redirect: ' . $this->POST['callback'];
    }

    private function uploadImage() {
        if ($this->FILES['image_file']) {
            // メインバナー画像 保存
            $this->image_url = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info, "cp_action_instagram_hashtag");
        } else {
            $this->image_url = $this->POST['image_url'];
        }
    }

    private function fillCpInstagramHashtagAction() {
        $this->cp_instagram_hashtag['title'] = $this->title;
        $this->cp_instagram_hashtag['image_url'] = $this->image_url;
        $this->cp_instagram_hashtag['text'] = $this->text;
        $this->cp_instagram_hashtag['button_label_text'] = $this->button_label_text;
        $this->cp_instagram_hashtag['skip_flg'] = $this->skip_flg ? $this->skip_flg : '';
        $this->cp_instagram_hashtag['autoload_flg'] = $this->autoload_flg ? $this->autoload_flg : '';
        $this->cp_instagram_hashtag['approval_flg'] = $this->approval_flg ? $this->approval_flg : '';
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpInstagramHashtagActionManager');
    }
}
