<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class CommentPluginShareSettings extends aafwEntityStoreBase {

    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;
    protected $_TableName = 'comment_plugin_share_settings';
    protected $_EntityName = "CommentPluginShareSetting";
}