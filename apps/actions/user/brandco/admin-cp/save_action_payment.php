<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpPaymentActionManager');

class save_action_payment extends BrandcoPOSTActionBase {
    protected $ContainerName = 'save_action_payment';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}',
    );

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
    );

    public function validate() {

        return true;
    }

    function doAction() {

        $data = array();
        $cp_payment_action_manager = new CpPaymentActionManager();
        $payment_action = $cp_payment_action_manager->getCpActions($this->POST['action_id']);

        $payment_action[0]->status = $this->POST['save_type'];
        $cp_payment_action_manager->updateCpActions($payment_action[0], $data);

        $this->Data['saved'] = 1;

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
        $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
        $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }

        $return = 'redirect: ' . $this->POST['callback'];

        return $return;
    }
}
