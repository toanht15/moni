<?php

AAFW::import('jp.aainc.aafw.base.aafwException');

class AuthTokenExpiredException extends aafwException {

    public function __construct($err_message) {
        parent::__construct($err_message, ErrorConstants::$ERROR_CODE["AUTH"]["AUTH_TOKEN_EXPIRED"]);
    }
}
