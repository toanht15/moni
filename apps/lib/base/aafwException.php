<?php

/**
 * Class aafwException
 */

AAFW::import('jp.aainc.classes.exception.ErrorConstants');

class aafwException extends Exception {

    protected $app_error_code;


    public function  __construct($message = "Application Error", $app_error_code = "ERROR_BASE_0001") {

        if (is_array($message)) {
            $this->message = $message['message'];
            $this->app_error_code = $message["app_error_code"];
        } else {
            $this->message = $message;
            $this->app_error_code = $app_error_code;
        }
    }

    /**
     * @return string
     */
    public function getAppErrorCode() {
        return $this->app_error_code;
    }
}
