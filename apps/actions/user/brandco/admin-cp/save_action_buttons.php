<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpButtonsActionManager');

class save_action_buttons extends BrandcoPOSTActionBase {

    protected $ContainerName = 'save_action_buttons';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    private $file_info = array();

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['text']['required'] = true;
            $this->ValidatorDefinition['moduleImage']['required'] = true;
            $this->ValidatorDefinition['title']['required'] = true;
        }
    }

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

    public function validate() {
        $split = explode(",", $this->POST['order']);
        foreach ($split as $li_id) {
            $button_info = explode("_",$li_id);
            if ($button_info[0] == 'newLi') {
                if ($this->POST['newOption'.$button_info[1]] == 'not_choose') {
                    $this->Validator->setError('newOption'.$button_info[1], 'NOT_CHOOSE');
                } elseif ($this->POST['save_type'] == CpAction::STATUS_FIX) {
                    if ($this->isEmpty($this->POST['newTitle'.$button_info[1]])) {
                        $this->Validator->setError('newTitle'.$button_info[1], 'NOT_REQUIRED');
                        $this->Form['action'] = explode('?mid=', $this->Form['action']);
                        $this->Form['action'] = $this->Form['action'][0].'?mid=action-not-filled';
                    }
                }
            } else {
                $this->Validator->setError('top_error', 'SAVE_ERROR');
            }
        }

        $this->Data['brand'] = $this->getBrand();
        $validatorService = new CpValidator($this->Data['brand']->id);
        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            return false;
        }

        if($this->FILES['image_file']){
            $fileValidator = new FileValidator($this->FILES['image_file'],FileValidator::FILE_TYPE_IMAGE);
            if(!$fileValidator->isValidFile()){
                $this->Validator->setError('image_file', 'NOT_MATCHES');
            }else{
                $this->file_info = $fileValidator->getFileInfo();
            }
        }

        if($this->Validator->getErrorCount()) return false;

        return true;
    }

    public function doAction() {

        $action_manager = new CpButtonsActionManager();
        $buttons_action = $action_manager->getCpActions($this->POST['action_id']);

        $data = array();
        if($this->FILES['image_file']){
            // メインバナー画像 保存
            $data['image_url'] = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/'.$this->Data['brand']->id . '/cp_action_button/' . StorageClient::getUniqueId()), $this->file_info
            );
        } else {
            $data['image_url'] = $this->POST['image_url'];
        }

        $split = explode(",", $this->POST['order']);
        $action_manager->deleteCpNextActionAndInfo($buttons_action[0]);
        $order = 0;
        foreach ($split as $li_id) {
            $button_info = explode("_",$li_id);
            if ($button_info[0] == 'newLi') {
                //create cp_next_action and cp_next_action_info
                $button_data['cp_next_action_id'] = $this->POST['newOption'.$button_info[1]];
                $button_data['label'] = $this->POST['newTitle'.$button_info[1]];
                $button_data['order'] = $order;
                $action_manager->createCpNextActionAndInfo($this->POST['action_id'], $button_data);
            }
            ++$order;
        }

        $data['text'] = $this->POST['text'];
        $data['title'] = $this->POST['title'];

        $buttons_action[0]->status = $this->POST['save_type'];

        $action_manager->updateCpActions($buttons_action[0], $data);

        $this->Data['saved'] = 1;

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }

        return 'redirect: ' . $this->POST['callback'];
    }
}
