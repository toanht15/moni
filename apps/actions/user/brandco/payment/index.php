<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.payment.GMOCommand');

class index extends BrandcoGETActionBase {

    public $NeedRedirect = true;
    public $NeedOption = array();

    public function doThisFirst() {
    }

    public function validate() {
        return true;
    }

    public function doAction() {

        $order_id = uniqid();

        $gmo_command = new GMOCommand();
        $entry_result = $gmo_command->getAccessIds($order_id);
        $exec_result = $gmo_command->execTran($entry_result, $order_id);

        return 'user/brandco/payment/index.php';
    }
}