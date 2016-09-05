<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class PopularVoteUserShareService extends aafwServiceBase {

    /** PopularVoteUserShares $popular_vote_user_share */
    private $popular_vote_user_shares;

    public function __construct() {
        $this->popular_vote_user_shares = $this->getModel('PopularVoteUserShares');
    }

    public function createEmptyPopularVoteUserShare() {
        return $this->popular_vote_user_shares->createEmptyObject();
    }

    public function updatePopularVoteUserShare($popular_vote_user_share) {
        $this->popular_vote_user_shares->save($popular_vote_user_share);
    }

    public function getPopularVoteUserShareByPopularVoteUserId($popular_vote_user_id) {
        $filter = array(
            'popular_vote_user_id' => $popular_vote_user_id,
            'del_flg' => 0
        );

        return $this->popular_vote_user_shares->find($filter);
    }

    public function getPopularVoteUserShareByPopularVoteUserIdAndSocialMediaType($popular_vote_user_id, $social_media_type) {
        $filter = array(
            'popular_vote_user_id' => $popular_vote_user_id,
            'social_media_type' => $social_media_type,
            'del_flg' => 0,
        );

        return $this->popular_vote_user_shares->findOne($filter);
    }

    public function deletePhysicalPopularVoteUserShareByPopularVoteUserId($popular_vote_user_id) {
        $popular_vote_user_share_array = $this->getPopularVoteUserShareByPopularVoteUserId($popular_vote_user_id);

        foreach ($popular_vote_user_share_array as $popular_vote_user_share) {
            $this->popular_vote_user_shares->deletePhysical($popular_vote_user_share);
        }
    }
}