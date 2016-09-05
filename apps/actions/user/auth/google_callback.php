<?php
AAFW::import('jp.aainc.aafw.aafwApplicationConfig');
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');

class google_callback extends aafwGETActionBase {

    public function doThisFirst() {
    }

    public function validate () {
        if(!$this->getSession('connectPath')) return false;
        return true;
    }

    function doAction() {
        $connectPath = $this->getSession('connectPath');
        $redirect_url = '';
        if ($this->code) {
            if (strpos($connectPath, '?')) {
                $redirect_url = $connectPath . '&code=' . $this->code;
            } else {
                $redirect_url = $connectPath . '?code=' . $this->code;
            }
        } else {
            if (strpos($connectPath, '?')) {
                $redirect_url = $connectPath . '&error=access_denied';
            } else {
                $redirect_url = $connectPath . '?error=access_denied';
            }
        }

        return 'redirect: ' . $redirect_url;
    }
}
