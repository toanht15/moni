<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class SocialLikeService extends aafwServiceBase {

    protected $social_likes;

    public function __construct() {
        $this->social_likes = $this->getModel("SocialLikes");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->bulder = new aafwDataBuilder(null,'w');
    }

    public function isLikedPage($monipla_user_id,$social_media_id,$like_id) {
        $filter = array(
            "user_id" => $monipla_user_id,
            "social_media_id" => $social_media_id,
            "like_id" => $like_id
        );
        if($this->social_likes->findOne($filter)) {
            return true;
        }
        return false;
    }

    public function isEmptyTable() {
        $social_likes = $this->bulder->getBySQL('SELECT id FROM social_likes LIMIT 1', array());
        if(!$social_likes[0]) {
            return true;
        }
        return false;
    }
}

