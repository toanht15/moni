<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpConversionTagActionManager');

class save_action_conversion_tag extends SaveActionBase {
    protected $ContainerName = 'save_action_conversion_tag';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    protected $ValidatorDefinition = array(
        'script_code'   => array(
            'type'      => 'str',
            'length'    => CpValidator::CV_TAG_MAX_LENGTH
        ),
    );

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['script_code']['required'] = true;
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

        if (!$this->validateDeadLine()) return false;

        return true;
    }

    function doAction() {

        $data = array();
        $data['title'] = $this->POST['title'] ? : '';
        $data['script_code'] = $this->POST['script_code'] ? : '';

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
        $this->cp_action_manager = $this->getService('CpConversionTagActionManager');
    }
}
