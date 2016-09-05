<?php

AAFW::import('jp.aainc.aafw.base.aafwException');

class APIException extends aafwException {

    public function __construct($err_message) {
        parent::__construct($err_message, ErrorConstants::$ERROR_CODE["API"]["API_ERROR"]);
    }
}
