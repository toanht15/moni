<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');

class save_action_retweet extends SaveActionBase {
    protected $ContainerName = 'save_action_retweet';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    public $file_info = array();
    public $tweet_content_id;
    public $cp_retweet_action_service;
    public $cp_retweet_message_service;
    public $tweet_contents = array();

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['text']['required'] = true;
            $this->ValidatorDefinition['title']['required'] = true;
            $this->ValidatorDefinition['moduleImage']['required'] = true;
            $this->ValidatorDefinition['tweet_url']['required'] = true;
        }
        $this->cp_retweet_action_service    = $this->createService('CpRetweetActionService');
        $this->cp_retweet_message_service   = $this->createService('CpRetweetMessageService');

        $this->fetchDeadLineValidator();
    }

    protected $ValidatorDefinition = array(
        'title'                         => array('type' => 'str', 'length' => 50),
        'image_url'                     => array('type' => 'str', 'length' => 512, 'validator' => array('URL')),
        'image_file'                    => array('type' => 'file', 'size' => '5MB'),
        'text'                          => array('type' => 'str', 'length' => CpValidator::MAX_TEXT_LENGTH),
        'tweet_url'                     => array('type' => 'str', 'length' => 512),
        'skip_flg'                      => array('type' => 'num', 'length' => 4),
        'twitter_name'                  => array('type' => 'str', 'length' => 255),
        'twitter_screen_name'           => array('type' => 'str', 'length' => 15),
        'twitter_profile_image_url'     => array('type' => 'str', 'length' => 512),
        'tweet_id'                      => array('type' => 'str', 'length' => 255),
        'tweet_date'                    => array('type' => 'str', 'length' => 255),
        'tweet_text'                    => array('type' => 'str', 'length' => CpValidator::SHORT_TEXT_LENGTH),
        'tweet_has_photo'               => array('type' => 'num', 'length' => 4),
        'tweet_photos'                  => array('type' => 'str', 'length' => CpValidator::SHORT_TEXT_LENGTH)
    );

    public function validate() {

        $brand = $this->getBrand();
        $validatorService = new CpValidator($brand->id);
        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }

        if($this->FILES['image_file']){
            $fileValidator = new FileValidator($this->FILES['image_file'],FileValidator::FILE_TYPE_IMAGE);
            if(!$fileValidator->isValidFile()){
                $this->Validator->setError('image_file', 'NOT_MATCHES');
                return false;
            }else{
                $this->file_info = $fileValidator->getFileInfo();
            }
        }

        if ($this->POST['tweet_url']) {
            $this->tweet_content_id = $this->cp_retweet_message_service->getTweetIdByTweetUrl($this->POST['tweet_url']);
            if ($this->tweet_content_id) {
                if ($this->tweet_content_id == $this->POST['tweet_id']) {
                    $this->tweet_contents['twitter_name']               = $this->POST['twitter_name'];
                    $this->tweet_contents['twitter_screen_name']        = $this->POST['twitter_screen_name'];
                    $this->tweet_contents['twitter_profile_image_url']  = $this->POST['twitter_profile_image_url'];
                    $this->tweet_contents['tweet_id']                   = $this->POST['tweet_id'];
                    $this->tweet_contents['tweet_text']                 = $this->POST['tweet_text'];
                    $this->tweet_contents['tweet_date']                 = $this->POST['tweet_date'];
                    if ($this->POST['tweet_has_photo']) {
                        $this->tweet_contents['tweet_has_photo']        = $this->POST['tweet_has_photo'];
                        $this->tweet_contents['tweet_photos']           = explode(',' ,$this->POST['tweet_photos']);
                    }
                } else {
                    $this->tweet_contents = $this->cp_retweet_message_service->getTweetContentByTweetId($this->tweet_content_id);
                    if (!$this->tweet_contents) {
                        $this->Validator->setError('tweet_url', 'INPUT_URL');
                        return false;
                    }
                }
            } else {
                $this->Validator->setError('tweet_url', 'INPUT_URL');
                return false;
            }
        }

        if (!$this->validateDeadLine()) return false;

        return true;
    }

    function doAction() {

        $data = array();
        if($this->FILES['image_file']){
            // メインバナー画像 保存
            $data['image_url'] = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info, "cp_action_retweet");
        } else {
            $data['image_url'] = $this->POST['image_url'];
        }

        $data['title']                      = $this->POST['title'];
        $data['text']                       = $this->POST['text'];
        $data['tweet_url']                  = $this->POST['tweet_url'];
        $data['tweet_id']                   = $this->tweet_contents['tweet_id'] ? : '';
        $data['tweet_text']                 = $this->tweet_contents['tweet_text'] ? : '';
        $data['tweet_date']                 = $this->tweet_contents['tweet_date'] ? : '';
        $data['twitter_name']               = $this->tweet_contents['twitter_name'] ? : '';
        $data['twitter_screen_name']        = $this->tweet_contents['twitter_screen_name'] ? : '';
        $data['twitter_profile_image_url']  = $this->tweet_contents['twitter_profile_image_url'] ? : '';
        $data['skip_flg']                   = $this->POST['skip_flg'] ? : 0;
        $data['tweet_has_photo']            = $this->tweet_contents['tweet_has_photo'] ? : 0;

        $this->getCpAction()->status        = $this->POST['save_type'];
        $this->renewDeadLineData();

        $this->getActionManager()->updateCpActions($this->getCpAction(), $data);

        //過去の画像を削除する
        $this->cp_retweet_action_service->deleteRetweetPhotoConfig($this->getConcreteAction()->id);

        if ($this->tweet_contents['tweet_has_photo']) {
            foreach ($this->tweet_contents['tweet_photos'] as $image_url) {
                $this->cp_retweet_action_service->createRetweetPhotoConfig($this->getConcreteAction()->id, $image_url);
            }
        }

        $this->Data['saved'] = 1;

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }

        $return = 'redirect: ' . $this->POST['callback'];

        return $return;
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpRetweetActionManager');
    }
}
