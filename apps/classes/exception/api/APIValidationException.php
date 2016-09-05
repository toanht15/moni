<?php

class APIValidationException extends Exception {

    protected $err_message;
    protected $err_code;

    public function __construct($err_message = '', $err_code = null) {

        $this->err_message = (is_array($err_message)) ? $err_message : [$err_message];
        $this->err_code = ($err_code) ? : ErrorConstants::$ERROR_CODE["API"]["API_VALIDATION_ERROR"];
    }

    public function getErrorCode() {
        return $this->err_code;
    }

    public function getErrorMessage() {
        return $this->err_message;
    }
}
