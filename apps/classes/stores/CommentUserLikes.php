<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class CommentUserLikes extends aafwEntityStoreBase {

    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;
    protected $_TableName = 'comment_user_likes';
    protected $_EntityName = "CommentUserLike";
}