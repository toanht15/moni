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
        file_get_contents('http://' . config('Domain.old_monipla_api') . $_SERVER['REQUEST_URI']);
        header('HTTP/1.1 301 Moved Permanently');
        header('X-Robots-Tag: noindex, nofollow');
        foreach($http_response_header as $header) {
            header($header);
        }
        exit;
    }
}