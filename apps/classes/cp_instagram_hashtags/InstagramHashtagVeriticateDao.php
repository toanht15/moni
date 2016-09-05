<?php

class InstagramHashtagVeriticateDao {

    private $cp_instagram_hashtag_action;
    private $cp_instagram_hashtags;
    // 1つのモジュールに設定したハッシュタグリスト
    private $hashtags = array();
    private $instagram_hashtag_users;
    private $instagram_hashtag_user_posts;
    private $next_min_id;
    // 次回使うpagination情報
    private $next_pagination;
    private $instagram_object;
    // APIで取得したhashtag情報
    private $tag_info;
    // manager access token
    private $access_token;
    private $cp;

    /**
     * @return mixed
     */
    public function getCpInstagramHashtagAction() {
        return $this->cp_instagram_hashtag_action;
    }

    /**
     * @param mixed $cp_instagram_hashtag_action
     */
    public function setCpInstagramHashtagAction($cp_instagram_hashtag_action) {
        $this->cp_instagram_hashtag_action = $cp_instagram_hashtag_action;
    }

    /**
     * @return mixed
     */
    public function getCpInstagramHashtags() {
        return $this->cp_instagram_hashtags;
    }

    /**
     * @param mixed $cp_instagram_hashtags
     */
    public function setCpInstagramHashtags($cp_instagram_hashtags) {
        $this->cp_instagram_hashtags = $cp_instagram_hashtags;
    }

    /**
     * @return array
     */
    public function getHashtags() {
        return $this->hashtags;
    }

    /**
     * @param array $hashtags
     */
    public function setHashtags($hashtags) {
        $this->hashtags = $hashtags;
    }

    /**
     * @return mixed
     */
    public function getInstagramHashtagUsers() {
        return $this->instagram_hashtag_users;
    }

    /**
     * @param mixed $instagram_hashtag_users
     */
    public function setInstagramHashtagUsers($instagram_hashtag_users) {
        $this->instagram_hashtag_users = $instagram_hashtag_users;
    }

    /**
     * @return mixed
     */
    public function getInstagramHashtagUserPosts() {
        return $this->instagram_hashtag_user_posts;
    }

    /**
     * @param mixed $instagram_hashtag_user_posts
     */
    public function setInstagramHashtagUserPosts($instagram_hashtag_user_posts) {
        $this->instagram_hashtag_user_posts = $instagram_hashtag_user_posts;
    }

    /**
     * @return mixed
     */
    public function getInstagramObject() {
        return $this->instagram_object;
    }

    /**
     * @param mixed $instagram_object
     */
    public function setInstagramObject($instagram_object) {
        $this->instagram_object = $instagram_object;
    }

    /**
     * @return mixed
     */
    public function getTagInfo() {
        return $this->tag_info;
    }

    /**
     * @param mixed $tag_info
     */
    public function setTagInfo($tag_info) {
        $this->tag_info = $tag_info;
    }

    /**
     * @return mixed
     */
    public function getNextMinId() {
        return $this->next_min_id;
    }

    /**
     * @param mixed $next_min_id
     */
    public function setNextMinId($next_min_id) {
        $this->next_min_id = $next_min_id;
    }

    /**
     * @return mixed
     */
    public function getAccessToken() {
        return $this->access_token;
    }

    /**
     * @param mixed $access_token
     */
    public function setAccessToken($access_token) {
        $this->access_token = $access_token;
    }

    /**
     * @return mixed
     */
    public function getNextPagination() {
        return $this->next_pagination;
    }

    /**
     * @param mixed $next_pagination
     */
    public function setNextPagination($next_pagination) {
        $this->next_pagination = $next_pagination;
    }

    /**
     * @return mixed
     */
    public function getCp() {
        return $this->cp;
    }

    /**
     * @param mixed $cp
     */
    public function setCp($cp) {
        $this->cp = $cp;
    }
    
    public function resetNextMinId(){
        $this->setNextMinId('');
    }
}