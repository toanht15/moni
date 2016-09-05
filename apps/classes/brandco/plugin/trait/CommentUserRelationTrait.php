<?php

trait CommentUserRelationTrait {
    protected $comment_user_relations;

    /**
     * @param $comment_user_relation
     */
    public function updateCommentUserRelation($comment_user_relation) {
        $this->comment_user_relations->save($comment_user_relation);
    }

    /**
     * @param $comment_user_relation_data
     * @return mixed
     */
    public function createCommentUserRelation($comment_user_relation_data) {
        $comment_user_relation = $this->comment_user_relations->createEmptyObject();

        $comment_user_relation->object_id = $comment_user_relation_data['object_id'];
        $comment_user_relation->object_type = $comment_user_relation_data['object_type'];
        $comment_user_relation->user_id = $comment_user_relation_data['user_id'];
        $comment_user_relation->anonymous_flg = CommentUserRelation::ANONYMOUS_FLG_OFF;
        $comment_user_relation->request_url = $comment_user_relation_data['request_url'];

        $this->comment_user_relations->save($comment_user_relation);

        return $comment_user_relation;
    }

    /**
     * @param $comment_user_relation_ids
     * @return mixed
     */
    public function getCommentUserRelationByIds($comment_user_relation_ids) {
        $filter = array(
            'id' => $comment_user_relation_ids
        );

        return $this->comment_user_relations->find($filter);
    }

    /**
     * @param $comment_user_relation_id
     * @return mixed
     */
    public function getCommentUserRelationById($comment_user_relation_id) {
        if (Util::isNullOrEmpty($comment_user_relation_id)) {
            return;
        }

        return $this->comment_user_relations->findOne($comment_user_relation_id);
    }

    /**
     * @param $object_id
     * @param $object_type
     * @return mixed
     */
    public function getCommentUserRelation($object_id, $object_type) {
        $filter = array(
            'object_id' => $object_id,
            'object_type' => $object_type
        );

        return $this->comment_user_relations->findOne($filter);
    }
}