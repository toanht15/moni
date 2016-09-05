<?php

AAFW::import('jp.aainc.aafw.base.aafwException');

class EntityNotFoundException extends aafwException {

    public function __construct($err_message) {
        parent::__construct(
            $err_message,
            ErrorConstants::$ERROR_CODE['BASE']['ENTITY_NOT_FOUND']
        );
    }
}
