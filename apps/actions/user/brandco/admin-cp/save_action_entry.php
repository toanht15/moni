<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpProfileQuestionnaireTrait');

class save_action_entry extends SaveActionBase {

    use CpProfileQuestionnaireTrait;

    protected $ContainerName = 'save_action_entry';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    private $file_info = array();

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['text']['required'] = true;
            $this->ValidatorDefinition['button_label_text']['required'] = true;
            $this->ValidatorDefinition['moduleImage']['required'] = true;
            $this->ValidatorDefinition['title']['required'] = true;
        }

        $this->setCheckedProfileQuestionnaireIds($this->POST);
    }

    protected $ValidatorDefinition = array(
        'title' => array(
            'type' => 'str',
            'length' => 80,
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
            'type' => 'str',
            'length' => 80
        )
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

        if (!$this->isValidChoice()) {
            $this->Validator->setError('prefill_flg', 'INVALID_VALUE');
        }

        return $this->Validator->isValid();
    }

    function doAction() {

        $data = array();
        if($this->FILES['image_file']){
            // メインバナー画像 保存
            $data['image_url'] = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info, "cp_action_entry");
        } else {
            $data['image_url'] = $this->POST['image_url'];
        }

        $data['text'] = $this->POST['text'];
        $data['title'] = $this->POST['title'];

        $data['button_label_text'] = $this->POST['button_label_text'];

        if ($this->canUpdateQuestionnaires()) {
            $service_factory = new aafwServiceFactory();
            /** @var CpEntryProfileQuestionnaireService $cp_profile_questionnaire_service */
            $cp_profile_questionnaire_service = $service_factory->create('CpEntryProfileQuestionnaireService');

            $cp_profile_questionnaire_service->clearQuestionnairesByCpActionId($this->getCpAction()->id);
            $checked_profile_questionnaire_ids = $this->getCheckedProfileQuestionnaireIds();

            foreach ($checked_profile_questionnaire_ids as $qst_id) {
                $cp_profile_questionnaire_service->addQuestionnaire($this->getCpAction()->id, $qst_id);
            }

            if (count($checked_profile_questionnaire_ids) > 0) {
                $this->getCpAction()->prefill_flg = $this->POST['prefill_flg'] == CpAction::PREFILL_FLG_FILL ? CpAction::PREFILL_FLG_FILL : CpAction::PREFILL_FLG_IGNORE;
            }
        }

        $this->getCpAction()->status = $this->POST['save_type'];
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
        $this->cp_action_manager = $this->getService('CpEntryActionManager');
    }
}