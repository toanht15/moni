<?php

AAFW::import('jp.aainc.aafw.base.aafwException');

class BadRequestException extends aafwException {

    public function __construct($err_message) {
        parent::__construct($err_message, ErrorConstants::$ERROR_CODE["BASE"]["BAD_REQUEST"]);
    }
}
