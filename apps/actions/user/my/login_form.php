<?php
/**
 * ドメイン移行対応
 */
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
class login_form extends aafwGETActionBase {

    public function validate () {
        return true;
    }

    function doAction() {
        return 'redirect: http://' . config('Domain.aaid') . $_SERVER['REQUEST_URI'];
    }
}