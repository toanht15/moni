<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');

class save_action_photo extends SaveActionBase {
    protected $ContainerName = 'save_action_photo';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    protected $brand = null;
    protected $file_info = array();

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
        'button_label_text' => array(
            'required' => true,
            'type' => 'str',
            'length' => 80
        ),
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

        if (!$this->validateDeadLine()) return false;

        return true;
    }

    function doAction() {
        $this->getCpAction()->status = $this->POST['save_type'];

        $data = array();

        $data['title'] = $this->POST['title'];
        $data['text'] = $this->POST['text'];
        $data['title_required'] = $this->POST['title_required'] ? $this->POST['title_required'] : 0;
        $data['comment_required'] = $this->POST['comment_required'] ? $this->POST['comment_required'] : 0;
        $data['fb_share_required'] = $this->POST['fb_share_required'] ? $this->POST['fb_share_required'] : 0;
        $data['tw_share_required'] = $this->POST['tw_share_required'] ? $this->POST['tw_share_required'] : 0;
        $data['panel_hidden_flg'] = $this->POST['panel_hidden_flg'] ? $this->POST['panel_hidden_flg'] : 0;
        $data['button_label_text'] = $this->POST['button_label_text'];
        $data['share_placeholder'] = $this->POST['share_placeholder'];

        if ($this->FILES['image_file']) {
            // メインバナー画像 保存
            $data['image_url'] = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info, "cp_action_photo");
        } else {
            $data['image_url'] = $this->POST['image_url'];
        }

        $this->renewDeadLineData();

        $this->getActionManager()->updateCpActions($this->getCpAction(), $data);

        $this->Data['saved'] = 1;

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->POST['callback'] = $this->POST['callback'] . '?mid=action-saved';
        } else {
            $this->POST['callback'] = $this->POST['callback'] . '?mid=action-draft';
        }

        return 'redirect: ' . $this->POST['callback'];
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpPhotoActionManager');
    }
}
