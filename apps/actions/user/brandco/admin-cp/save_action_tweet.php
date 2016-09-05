<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');

class save_action_tweet extends SaveActionBase {
    protected $ContainerName = 'save_action_tweet';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    private $file_info = array();

    protected $ValidatorDefinition = array(
        'title' => array(
            'type' => 'str',
            'length' => 50,
        ),
        'image_url' => array(
            'type' => 'str',
            'length' => 512,
            'validator' => array('URL')
        ),
        'image_file' => array(
            'type' => 'file',
            'size' => '5MB'
        ),
        'text' => array(
            'type' => 'str',
            'length' => CpValidator::MAX_TEXT_LENGTH
        ),
        'tweet_default_text' => array(
            'type' => 'str',
            'length' => CpValidator::SHORT_TEXT_LENGTH,
        ),
        'tweet_fixed_text' => array(
            'type' => 'str',
            'length' => CpValidator::SHORT_TEXT_LENGTH,
        ),
        'photo_flg' => array(
            'type' => 'num',
            'length' => 4
        ),
        'skip_flg' => array(
            'type' => 'num',
            'length' => 4
        ),
    );

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['text']['required'] = true;
            $this->ValidatorDefinition['title']['required'] = true;
            $this->ValidatorDefinition['moduleImage']['required'] = true;
        }

        $this->fetchDeadLineValidator();
    }

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

        if (!$this->validateDeadLine()) return false;

        $tweet_length = 0;
        $tweet_length += $this->POST['tweet_default_text'] ? $this->getTwitterStringLength($this->POST['tweet_default_text']) : 0 ;
        $tweet_length += $this->POST['tweet_fixed_text'] ? $this->getTwitterStringLength($this->POST['tweet_fixed_text']) + CpTweetAction::NEW_LINE_LENGTH : 0 ;
        $tweet_length += $this->POST['photo_flg'] == CpTweetAction::PHOTO_REQUIRE ? CpTweetAction::PHOTO_TEXT_LENGTH : 0 ;

        return $tweet_length <= CpTweetAction::MAX_TEXT_LENGTH;
    }

    function doAction() {

        $data = array();
        if($this->FILES['image_file']){
            // メインバナー画像 保存
            $data['image_url'] = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info, "cp_action_tweet");
        } else {
            $data['image_url'] = $this->POST['image_url'];
        }

        $data['title']                  = $this->POST['title'];
        $data['text']                   = $this->POST['text'];
        $data['tweet_default_text']     = $this->POST['tweet_default_text'];
        $data['tweet_fixed_text']       = $this->POST['tweet_fixed_text'];
        $data['photo_flg']              = $this->POST['photo_flg'] ? : 0;
        $data['skip_flg']               = $this->POST['skip_flg'] ? : 0;

        $this->getCpAction()->status        = $this->POST['save_type'];
        $this->renewDeadLineData();

        $this->getActionManager()->updateCpActions($this->getCpAction(), $data);

        $this->Data['saved'] = 1;

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
        $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
        $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }

        $return = 'redirect: ' . $this->POST['callback'];

        return $return;
    }

    private function getTwitterStringLength($tweet_text) {
        $tweet_text = str_replace("\r\n", "\n", $tweet_text);
        $tweet_text_length = mb_strlen($tweet_text, 'UTF-8');
        $arrays = preg_split("/\n| |　/", $tweet_text);
        foreach ($arrays as $element) {
            if ($this->checkTextIsUrl($element)) {
                $tweet_text_length += CpTweetAction::URL_TEXT_LENGTH - mb_strlen($element, 'UTF-8');
            }
        }
        return $tweet_text_length;
    }

    private function checkTextIsUrl($tweet_text) {
        return preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $tweet_text);
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpTweetActionManager');
    }
}
