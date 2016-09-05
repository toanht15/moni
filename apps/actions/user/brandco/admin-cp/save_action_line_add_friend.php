<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpConversionTagActionManager');

class save_action_line_add_friend extends SaveActionBase {

    protected $ContainerName = 'save_action_line_add_friend';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    protected $ValidatorDefinition = array(
        'line_account_id' => array(
            'type' => 'str',
            'length' => 50
        ),
        'line_account_name' => array(
            'type' => 'str',
            'length' => 255
        ),
        'comment' => array(
            'type' => 'str',
            'length' => CpValidator::SHORT_TEXT_LENGTH
        ),
    );

    public function doThisFirst() {

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['line_account_id']['required'] = true;
            $this->ValidatorDefinition['line_account_name']['required'] = true;
            $this->ValidatorDefinition['comment']['required'] = true;
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

        return $this->validateDeadLine();
    }

    function doAction() {

        $data = array();
        $data['title'] = '「'. ($this->POST['line_account_name'] ? $this->POST['line_account_name'].'」のLINEアカウントを友だちに追加しよう！' : '');
        $data['line_account_id'] = $this->POST['line_account_id'] ? : '';
        $data['line_account_name'] = $this->POST['line_account_name'] ? : '';
        $data['comment'] = $this->POST['comment'] ? : '';

        $this->getCpAction()->status = $this->POST['save_type'];
        $this->renewDeadLineData();

        $this->getActionManager()->updateCpActions($this->getCpAction(), $data);

        $this->Data['saved'] = 1;

        $redirect_url = $this->POST['callback'] . '?mid=' . ($this->POST['save_type'] == CpAction::STATUS_FIX ? 'action-saved' : 'action-draft');

        return 'redirect: ' . $redirect_url;
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpLineAddFriendActionManager');
    }
}
