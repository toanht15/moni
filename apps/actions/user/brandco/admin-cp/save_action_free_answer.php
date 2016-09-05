<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpEntryActionManager');

class save_action_free_answer extends SaveActionBase {
    protected $ContainerName = 'save_action_free_answer';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    private $file_info = array();

    protected $ValidatorDefinition = array(
        'title' => array(
            'type' => 'str',
            'length' => 50
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
        'button_label' => array(
            'type' => 'str',
            'length' => 80
        )
    );

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['text']['required'] = true;
            $this->ValidatorDefinition['button_label']['required'] = true;
            $this->ValidatorDefinition['moduleImage']['required'] = true;
            $this->ValidatorDefinition['title']['required'] = true;
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

        /** @var QuestionNgWordService $questionNgWordService */
        $questionNgWordService = $this->createService('QuestionNgWordService');
        if ($questionNgWordService->isNgQuestion($this->POST['text'], $this->brand->id)) {
            $this->Validator->setError('text', 'NG_QUESTION');
            return false;
        }

        return true;
    }

    function doAction() {

        if($this->FILES['image_file']){
            // メインバナー画像 保存
            $data['image_url'] = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info, "cp_action_free_answer");
        } else {
            $data['image_url'] = $this->POST['image_url'];
        }

        $data['question'] = $this->POST['text'];
        $data['button_label'] = $this->POST['button_label'];
        $data['title'] = $this->POST['title'];

        $this->getCpAction()->status = $this->POST['save_type'];
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

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpFreeAnswerActionManager');
    }
}
