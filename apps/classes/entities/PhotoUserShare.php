<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class PhotoUserShare extends aafwEntityBase {
    protected $_TableName = 'photo_user_shares';
    protected $_EntityName = 'PhotoUserShare';

    const SHARE_TEXT_LENGTH = 50;

    const SEARCH_EXISTS = 1;
    const SEARCH_NOT_EXISTS = 0;
}
