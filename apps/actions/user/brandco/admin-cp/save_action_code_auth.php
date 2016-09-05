<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');
AAFW::import('jp.aainc.classes.entities.CpAction');

class save_action_code_auth extends SaveActionBase {
    protected $ContainerName = 'save_action_code_auth';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    protected $ValidatorDefinition = array(
        'title'             => array('type' => 'str', 'length' => 50),
        'image_url'         => array('type' => 'str', 'length' => 512, 'validator' => array('URL')),
        'image_file'        => array('type' => 'file', 'size' => '5MB'),
        'text'              => array('type' => 'str', 'length' => CpValidator::MAX_TEXT_LENGTH),
        'code_auth_id'      => array('type' => 'num'),
        'min_code_flg'      => array('type' => 'num', 'range' => array('>=' => 1, '<=' => 2)),
        'min_code_count'    => array('type' => 'num', 'range' => array('>=' => 1)),
        'max_code_count'    => array('type' => 'num', 'range' => array('>=' => 1)),
        'max_code_flg'      => array('type' => 'num', 'range' => array('>=' => 1, '<=' => 2))
    );

    private $file_info = array();

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['text']['required'] = true;
            $this->ValidatorDefinition['code_auth_id']['required'] = true;
            $this->ValidatorDefinition['moduleImage']['required'] = true;
            $this->ValidatorDefinition['title']['required'] = true;
            $this->ValidatorDefinition['min_code_flg'] = true;
            $this->ValidatorDefinition['max_code_flg'] = true;
        }

        $this->fetchDeadLineValidator();
    }

    public function validate() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX && $this->POST['code_auth_id'] == 0) {
            $this->Validator->setError('code_auth_id', 'NOT_CHOOSE');
            return false;
        }

        $validatorService = new CpValidator($this->getBrand()->id);
        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }

        if($this->FILES['image_file']){
            $fileValidator = new FileValidator($this->FILES['image_file'], FileValidator::FILE_TYPE_IMAGE);
            if(!$fileValidator->isValidFile()){
                $this->Validator->setError('image_file', 'NOT_MATCHES');
                return false;
            }else{
                $this->file_info = $fileValidator->getFileInfo();
            }
        }

        if ($this->POST['code_auth_id']) {
            /** @var CodeAuthenticationService $code_auth_service */
            $code_auth_service = $this->createService('CodeAuthenticationService');
            $code_auth = $code_auth_service->getCodeAuthById($this->POST['code_auth_id']);
            if (!$code_auth || $code_auth->brand_id != $this->getBrand()->id) {
                return '404';
            }

            if ($this->POST['min_code_count'] != 0 && $this->POST['max_code_count'] != 0 && $this->POST['min_code_count'] > $this->POST['max_code_count']) {
                $this->Validator->setError('max_code_count2', 'INVALID_PARAM1');
                return false;
            }
        }

        if (!$this->validateDeadLine()) return false;

        return true;
    }

    function doAction() {

        if($this->FILES['image_file']){
            $this->POST['image_url'] = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info, "cp_action_code_auth");
        }

        $this->getCpAction()->status = $this->POST['save_type'];
        $this->renewDeadLineData();

        $this->getActionManager()->updateCpActions($this->getCpAction(), $this->POST);

        $this->Data['saved'] = 1;

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }

        return 'redirect: ' . $this->POST['callback'];
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpCodeAuthActionManager');
    }
}
