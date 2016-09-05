<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CodeAuthUserTrackingLog extends aafwEntityBase {

    const AUTH_ERROR_DUPLICATED_CODE    = 'DUPLICATED_CODE';
    const AUTH_ERROR_NOT_EXIST_CODE     = 'NOT_EXIST_CODE';
    const AUTH_ERROR_EXPIRED_CODE       = 'EXPIRED_CODE';
    const AUTH_ERROR_NOT_REQUIRED       = 'NOT_REQUIRED';
    const AUTH_ERROR_CODE_TOO_LONG      = 'TOO_LONG';

    public static $untracking_errors = array(
        self::AUTH_ERROR_NOT_REQUIRED,
        self::AUTH_ERROR_DUPLICATED_CODE
    );
}