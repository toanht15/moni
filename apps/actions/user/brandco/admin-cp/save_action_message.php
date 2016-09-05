<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');

class save_action_message extends SaveActionBase {
    protected $ContainerName = 'save_action_message';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    public $file_info = array();

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
        )
    );

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['text']['required'] = true;
            $this->ValidatorDefinition['moduleImage']['required'] = true;
            $this->ValidatorDefinition['title']['required'] = true;
        }

        $this->fetchDeadLineValidator();
    }

    public function validate() {
        $this->Data['brand'] = $this->getBrand();
        $validatorService = new CpValidator($this->Data['brand']->id);
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

        return true;
    }

    function doAction() {

        $data = array();
        if($this->FILES['image_file']){
            // メインバナー画像 保存
            $data['image_url'] = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info, "cp_action_message");
        } else {
            $data['image_url'] = $this->POST['image_url'];
        }

        $data['text'] = $this->POST['text'];
        $data['title'] = $this->POST['title'];
        $data['manual_step_flg'] = $this->POST['manual_step_flg'] ? Cp::FLAG_SHOW_VALUE : Cp::FLAG_HIDE_VALUE;
        $data['send_text_mail_flg'] = $this->POST['send_text_mail_flg'] ? Cp::CRM_SEND_TEXT_MAIL_FLG_ON : Cp::CRM_SEND_TEXT_MAIL_FLG_OFF;

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
        $this->cp_action_manager = $this->getService('CpMessageActionManager');
    }
}
