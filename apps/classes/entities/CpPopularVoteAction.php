<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class CpPopularVoteAction extends aafwEntityBase {
    protected $_Relations = array(
        'CpActions' => array(
            'cp_action_id' => 'id'
        ),
        'CpPopularVoteCandidates' => array(
            'id' => 'cp_popular_vote_action_id'
        )
    );

    const FILE_TYPE_IMAGE = 1;
    const FILE_TYPE_MOVIE = 2;

    const RANDOM_FLG = 1;
    const RANKING_FLG = 1;

    const SHARE_URL_TYPE_CP = 1;
    const SHARE_URL_TYPE_RANKING = 2;
}
