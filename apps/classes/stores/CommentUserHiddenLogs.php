<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class CommentUserHiddenLogs extends aafwEntityStoreBase {

    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;
    protected $_TableName = 'comment_user_hidden_logs';
    protected $_EntityName = "CommentUserHiddenLog";
}