<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CommentPluginAction extends aafwEntityBase {

    const REQUIREMENT_FLG_OFF   = "0";
    const REQUIREMENT_FLG_ON    = "1";

    const COMMENT_PLUGIN_ACTION_TYPE_FREETEXT   = 1;
    const COMMENT_PLUGIN_ACTION_TYPE_REVIEW     = 2;
    const COMMENT_PLUGIN_ACTION_TYPE_CHOICE     = 3;
}
