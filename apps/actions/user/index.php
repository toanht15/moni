<?php
/**
 * ドメイン移行対応
 */
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
class index extends aafwGETActionBase {

    public function validate () {
        return true;
    }

    function doAction() {
        return 'redirect: http://' . config('Domain.monipla') . $_SERVER['REQUEST_URI'];
    }
}