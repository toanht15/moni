<?php
/**
 * ドメイン移行対応
 */
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
class maintenance extends aafwGETActionBase {

    public function validate () {
        return true;
    }

    function doAction() {
        return 'redirect: https://' . config('Domain.monipla') . '/help' . $_SERVER['REQUEST_URI'];
    }
}