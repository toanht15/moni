<?php
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');

class instagram_callback extends aafwGETActionBase {

    public function validate() {
        return $_SESSION['instagram_redirect_url'];
    }

    function doAction() {
        $redirect_url = $_SESSION['instagram_redirect_url'];
        $params = $this->code ?
            (strpos($redirect_url, '?') ? '&code=' . $this->code : '?code=' . $this->code) :
            '?error=access_denied';

        $redirect = 'redirect: ' . $_SESSION['instagram_redirect_url'] . $params;

        if ($_SESSION['callback_param']) {
            $redirect .= '&callback_url='.$_SESSION['callback_param'];
        }

        return $redirect;
    }
}
