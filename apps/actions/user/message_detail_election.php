<?php
/**
 * ドメイン移行対応
 */
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
class message_detail_election extends aafwGETActionBase {

    public function validate () {
        return true;
    }

    function doAction() {
        $uri = preg_replace('#^\/message_detail_election#', '/campaign/elected', $_SERVER['REQUEST_URI']);
        return 'redirect: http://' . config('Domain.monipla_fb_app') . $uri;
    }
}