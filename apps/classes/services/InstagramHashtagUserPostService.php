<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');

class InstagramHashtagUserPostService extends aafwServiceBase {

    /** @var InstagramHashtagUserPosts instagram_hashtag_user_posts */
    private $instagram_hashtag_user_posts;

    public function __construct() {
        $this->instagram_hashtag_user_posts = $this->getModel('InstagramHashtagUserPosts');
    }

    public function getInstagramHashtagUserPostById($id) {
        $filter = array(
            'id' => $id
        );
        return $this->instagram_hashtag_user_posts->findOne($filter);
    }

    public function getInstagramHashtagUserPostByInstagramHashtagUserIdAndObjectId($instagram_hashtag_user_id, $object_id) {
        if (!$instagram_hashtag_user_id || !$object_id) return array();

        $filter = array(
            'instagram_hashtag_user_id' => $instagram_hashtag_user_id,
            'object_id' => $object_id
        );
        return $this->instagram_hashtag_user_posts->findOne($filter);
    }

    public function createEmptyObject() {
        return $this->instagram_hashtag_user_posts->createEmptyObject();
    }

    public function saveInstagramHashtagUserPost(InstagramHashtagUserPost $instagram_hashtag_user_post) {
        return $this->instagram_hashtag_user_posts->save($instagram_hashtag_user_post);
    }

    public function getInstagramHashtagUserPostsByObjectId($object_id) {
        if (!$object_id) return array();
        $filter = array(
            'object_id' => $object_id
        );
        return $this->instagram_hashtag_user_posts->find($filter);
    }
}
