<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class InstagramHashtagUser extends aafwEntityBase {

    const SEARCH_EXISTS = 1;
    const SEARCH_NOT_EXISTS = 0;

    protected $_Relations = array(
        'InstagramHashtagUserPosts' => array(
            'id' => 'instagram_hashtag_user_id'
        ),
        'CpUsers' => array(
            'cp_user_id' => 'id'
        )
    );

    public function isValidPostTime($instagram_hashtag_post_time) {
        if (!$instagram_hashtag_post_time || !intval($instagram_hashtag_post_time)) return false;

        // 参加時の時間
        $created_at = date('Y-m-d H:i', strtotime($this->created_at));

        // 投稿時の時間
        $instagram_hashtag_post_date_time = date('Y-m-d H:i', $instagram_hashtag_post_time);

        return $created_at <= $instagram_hashtag_post_date_time;
    }
}
