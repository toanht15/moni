<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.PopularVoteUser');

class PopularVoteUserService extends aafwServiceBase {

    /** PopularVoteUsers $popular_vote_users */
    private $popular_vote_users;

    public function __construct() {
        $this->popular_vote_users = $this->getModel('PopularVoteUsers');
    }

    public function createEmptyPopularVoteUser() {
        return $this->popular_vote_users->createEmptyObject();
    }

    public function updatePopularVoteUser($popular_vote_user) {
        return $this->popular_vote_users->save($popular_vote_user);
    }

    public function getPopularVoteUserByIds($cp_action_id, $cp_user_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'cp_user_id' => $cp_user_id,
            'del_flg' => 0
        );

        return $this->popular_vote_users->findOne($filter);
    }

    public function getPopularVoteUserByCpActionId($cp_action_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'del_flg' => 0
        );

        return $this->popular_vote_users->find($filter);
    }

    public function getPopularVoteUserByCpActionIdAndCpUserId($cp_action_id, $cp_user_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'cp_user_id' => $cp_user_id
        );

        return $this->popular_vote_users->find($filter);
    }

    public function countPopularVoteUserByCpPopularVoteCandidateId($cp_popular_vote_candidate_id) {
        $filter = array(
            'cp_popular_vote_candidate_id' => $cp_popular_vote_candidate_id,
            'del_flg' => 0
        );

        return $this->popular_vote_users->count($filter);
    }

    public function deletePhysicalPopularVoteUserByCpActionId($cp_action_id) {
        $popular_vote_user_share_service = $this->getService('PopularVoteUserShareService');

        $popular_vote_user_array = $this->getPopularVoteUserByCpActionId($cp_action_id);
        foreach ($popular_vote_user_array as $popular_vote_user) {
            $popular_vote_user_share_service->deletePhysicalPopularVoteUserShareByPopularVoteUserId($popular_vote_user->id);
            $this->popular_vote_users->deletePhysical($popular_vote_user);
        }
    }

    public function deletePhysicalPopularVoteUserByCpActionIdAndCpUserId($cp_action_id, $cp_user_id) {
        /** @var PopularVoteUserShareService $popular_vote_user_share_service */
        $popular_vote_user_share_service = $this->getService('PopularVoteUserShareService');

        $popular_vote_user_array = $this->getPopularVoteUserByCpActionIdAndCpUserId($cp_action_id, $cp_user_id);
        foreach ($popular_vote_user_array as $popular_vote_user) {
            $popular_vote_user_share_service->deletePhysicalPopularVoteUserShareByPopularVoteUserId($popular_vote_user->id);
            $this->popular_vote_users->deletePhysical($popular_vote_user);
        }
    }
}
