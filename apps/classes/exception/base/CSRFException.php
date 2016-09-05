<?php


AAFW::import('jp.aainc.aafw.base.aafwException');
AAFW::import('jp.aainc.classes.exception.base.BadRequestException');

class CSRFException extends BadRequestException {

	public function __construct($err_message) {
		parent::__construct($err_message, ErrorConstants::$ERROR_CODE["BASE"]["BAD_REQUEST"]);
	}
}
