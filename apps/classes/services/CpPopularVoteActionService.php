<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');

class CpPopularVoteActionService extends aafwServiceBase {

    /** CpPopularVoteActions $cp_popular_vote_actions */
    private $cp_popular_vote_actions;
    /** CpPopularVoteCandidates $cp_popular_vote_candidates */
    private $cp_popular_vote_candidates;

    public function __construct() {
        $this->cp_popular_vote_actions = $this->getModel('CpPopularVoteActions');
        $this->cp_popular_vote_candidates = $this->getModel('CpPopularVoteCandidates');
    }

    /**
     * @return mixed
     */
    public function createEmptyCpPopularVoteCandidate() {
        return $this->cp_popular_vote_candidates->createEmptyObject();
    }

    /**
     * @param $cp_popular_vote_action_id
     * @return mixed
     */
    public function getCpPopularVoteActionById($cp_popular_vote_action_id) {
        $filter = array(
            'id' => $cp_popular_vote_action_id,
            'del_flg' => 0
        );

        return $this->cp_popular_vote_actions->findOne($filter);
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpPopularVoteActionByCpActionId($cp_action_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'del_flg' => 0
        );

        return $this->cp_popular_vote_actions->findOne($filter);
    }

    /**
     * @param $cp_popular_vote_action_id
     * @param $cp_popular_vote_candidate_id
     * @return mixed
     */
    public function getCpPopularVoteCandidateByIds($cp_popular_vote_action_id, $cp_popular_vote_candidate_id) {
        $filter = array(
            'id' => $cp_popular_vote_candidate_id,
            'cp_popular_vote_action_id' => $cp_popular_vote_action_id,
            'del_flg' => 0
        );

        return $this->cp_popular_vote_candidates->findOne($filter);
    }

    /**
     * @param $cp_popular_vote_candidate_id
     * @return mixed
     */
    public function getCpPopularVoteCandidateById($cp_popular_vote_candidate_id) {
        $filter = array(
            'id' => $cp_popular_vote_candidate_id,
            'del_flg' => 0
        );

        return $this->cp_popular_vote_candidates->findOne($filter);
    }

    /**
     * @param $cp_popular_vote_action_id
     * @return mixed
     */
    public function getCpPopularVoteCandidateByCpPopularVoteActionId($cp_popular_vote_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_popular_vote_action_id' => $cp_popular_vote_action_id,
                'del_flg' => 0
            ),
            'order' => array(
                'name' => 'order_no',
                'direction' => 'asc'
            )
        );

        return $this->cp_popular_vote_candidates->find($filter);
    }

    /**
     * @param $file_type
     * @param $cp_popular_vote_action
     * @return mixed
     */
    public function updateFileTypeByCpPopularVoteAction($file_type, $cp_popular_vote_action) {
        $cp_popular_vote_action->file_type = $file_type;

        return $this->cp_popular_vote_actions->save($cp_popular_vote_action);
    }

    /**
     * @param $cp_popular_vote_candidate
     * @return mixed
     */
    public function updateCpPopularVoteCandidate($cp_popular_vote_candidate) {
        return $this->cp_popular_vote_candidates->save($cp_popular_vote_candidate);
    }

    /**
     * @param $cp_popular_vote_candidate_id
     * @return mixed
     */
    public function deleteCpPopularVoteCandidateById($cp_popular_vote_candidate_id) {
        $cp_popular_vote_candidate = $this->getCpPopularVoteCandidateById($cp_popular_vote_candidate_id);
        $cp_popular_vote_candidate->del_flg = 1;

        return $this->cp_popular_vote_candidates->save($cp_popular_vote_candidate);
    }

    /**
     * @param $cp_popular_vote_action_id
     */
    public function deleteCpPopularVoteCandidateByCpPopularVoteActionId($cp_popular_vote_action_id) {
        $cp_popular_vote_candidate_array = $this->getCpPopularVoteCandidateByCpPopularVoteActionId($cp_popular_vote_action_id);

        foreach ($cp_popular_vote_candidate_array as $cp_popular_vote_candidate) {
            $cp_popular_vote_candidate->del_flg = 1;
            $this->cp_popular_vote_candidates->save($cp_popular_vote_candidate);
        }
    }
}