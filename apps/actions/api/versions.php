<?php

AAFW::import('jp.aainc.classes.action.APIActionBase');

class versions extends APIActionBase {

    const API_VERSION = "1.0";

    public $allow_methods = [
        self::HTTP_METHOD_GET,
    ];

    public function validate() {
        return true;
    }

    function doAction() {

        $result = [
            "version" => self::API_VERSION
        ];

        $this->assign('json_data', $result);
        return 'dummy.php';
    }
}