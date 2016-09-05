<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class PopularVoteUser extends aafwEntityBase {
    protected $_Relations = array(
        'CpActions' => array(
            'cp_action_id' => 'id'
        ),
        'CpUsers' => array(
            'cp_user_id' => 'id'
        ),
        'CpPopularVoteCandidates' => array(
            'cp_popular_vote_candidate_id' => 'id'
        ),
        'PopularVoteUserShares' => array(
            'id' => 'popular_vote_user_id'
        )
    );
}
