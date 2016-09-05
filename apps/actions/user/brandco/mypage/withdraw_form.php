<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.WithdrawLog');

class withdraw_form extends BrandcoGETActionBase {
    public $NeedOption = array();
    public $NeedUserLogin = true;
    protected $ContainerName = 'withdraw';

    public function validate() {
        return true;
    }

    public function doAction() {

        return 'user/brandco/mypage/withdraw_form.php';
    }
}