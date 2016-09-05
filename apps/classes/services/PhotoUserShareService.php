<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class PhotoUserShareService extends aafwServiceBase {

    /** @var PhotoUserShares $photo_user_shares */
    protected $photo_user_shares;

    public function __construct() {
        $this->photo_user_shares = $this->getModel('PhotoUserShares');
    }

    public function createEmptyObject() {
        return $this->photo_user_shares->createEmptyObject();
    }

    public function update($photo_user_share) {
        $this->photo_user_shares->save($photo_user_share);
    }

    public function getPhotoUserSharesByPhotoUserIdAndSnsType($photo_user_id, $social_media_type) {
        $filter = array(
            'photo_user_id' => $photo_user_id,
            'social_media_type' => $social_media_type
        );
        return $this->photo_user_shares->findOne($filter);
    }
}
