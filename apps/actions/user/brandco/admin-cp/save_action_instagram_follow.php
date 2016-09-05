<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');

class save_action_instagram_follow extends SaveActionBase {
    protected $ContainerName = 'save_action_instagram_follow';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    protected $ValidatorDefinition = array(
        'brand_social_account_id' => array(
            'type' => 'num',
        ),
        'current_entry_id' => array(
            'type' => 'num',
            'range' => array(
                '>=' => 1
            )
        )
    );

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['brand_social_account_id']['required'] = true;
            $this->ValidatorDefinition['current_entry_id']['required'] = true;
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
        $data['brand_social_account_id'] = $this->brand_social_account_id;
        $data['instagram_entry_id'] = $this->current_entry_id;

        $this->getCpAction()->status = $this->POST['save_type'];
        $this->renewDeadLineData();

        $this->getActionManager()->updateCpActions($this->getCpAction(), $data);

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }

        $this->Data['saved'] = 1;

        $return = 'redirect: ' . $this->POST['callback'];

        return $return;
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpInstagramFollowActionManager');
    }
}
