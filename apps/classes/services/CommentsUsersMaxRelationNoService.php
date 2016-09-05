<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CommentsUsersMaxRelationNoService extends aafwServiceBase {

    private $comments_users_max_relation_no_store;

    public function __construct() {
        $this->comments_users_max_relation_no_store = $this->getModel('CommentsUsersMaxRelationNos');
    }

    public function getMaxNoByCommentPluginIdForUpdate($comment_plugin_id) {

        $filter = array(
            'comment_plugin_id' => $comment_plugin_id,
            'for_update' => true
        );

        return $this->comments_users_max_relation_no_store->findOne($filter);
    }

    public function setMaxNo($comments_users_max_relation_no) {
        $this->comments_users_max_relation_no_store->save($comments_users_max_relation_no);
    }
}
