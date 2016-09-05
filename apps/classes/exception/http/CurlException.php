<?php


AAFW::import('jp.aainc.aafw.base.aafwException');

class CurlException extends aafwException {

	public function __construct($err_message) {
		parent::__construct($err_message, ErrorConstants::$ERROR_CODE["HTTP"]["HTTP_ERROR"]);
	}
}
