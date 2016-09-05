<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class MultiPostSnsQueue extends aafwEntityBase {

    const CALLBACK_UPDATE_PHOTO_USER_SHARE          = 1;
    const CALLBACK_UPDATE_POPULAR_VOTE_USER_SHARE   = 2;
    const CALLBACK_UPDATE_COMMENT_USER_SHARE        = 3;

    const EXECUTE_STATUS_SUCCESS = '1';
    const EXECUTE_STATUS_ERROR = '2';

    const ERROR_FLG_OFF = 0;
    const ERROR_FLG_ON  = 1;

    public static $callback_function = array(
        self::CALLBACK_UPDATE_PHOTO_USER_SHARE          => 'updatePhotoUserShare',
        self::CALLBACK_UPDATE_POPULAR_VOTE_USER_SHARE   => 'updatePopularVoteUserShare',
        self::CALLBACK_UPDATE_COMMENT_USER_SHARE        => 'updateCommentUserShare'
    );
}
