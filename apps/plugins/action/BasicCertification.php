<?php

AAFW::import('jp.aainc.aafw.base.aafwActionPluginBase');

class BasicCertification extends aafwActionPluginBase {

    protected $HookPoint = 'Plugin/Zero';
    protected $Priority = 1;

    public function doService() {

        $settings = aafwApplicationConfig::getInstance();
        if ($settings->BasicCertification) {
            $user = 'brandco';
            $password = 'brandco123';

            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                header('WWW-Authenticate: Basic realm="Private Page"');
                header('HTTP/1.0 401 Unauthorized');

                die('このページを見るにはログインが必要です');
            } else {
                if ($_SERVER['PHP_AUTH_USER'] != $user
                    || $_SERVER['PHP_AUTH_PW'] != $password
                ) {
                    header('WWW-Authenticate: Basic realm="Private Page"');
                    header('HTTP/1.0 401 Unauthorized');

                    die('このページを見るにはログインが必要です');
                }
            }
        }
        return;
    }
}
