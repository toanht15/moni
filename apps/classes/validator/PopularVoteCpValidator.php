<?php
AAFW::import('jp.aainc.classes.CpInfoContainer');

class PopularVoteCpValidator extends CpValidator {
    private $cp;
    private $cp_action_id;
    private $cp_concrete_action;

    /** @var  CpPopularVoteActionManager $cp_popular_vote_action_manager */
    private $cp_popular_vote_action_manager;
    /** @var  CpPopularVoteActionService $cp_popular_vote_action_service */
    private $cp_popular_vote_action_service;

    public function __construct($brand_id, $cp_action_id) {
        parent::__construct($brand_id);

        $this->cp_action_id = $cp_action_id;
        $this->cp_popular_vote_action_manager = new CpPopularVoteActionManager();
        $this->cp_popular_vote_action_service = $this->getService('CpPopularVoteActionService');
    }

    /**
     * @return bool
     */
    public function getCp() {
        if ($this->cp) {
            return $this->cp;
        }

        if ($this->getCpAction() == false) {
            return false;
        }

        if (!$this->getCpAction()->cp_action_group_id) {
            return false;
        }

        $cp_action_group = $this->getCpAction()->getCpActionGroup();
        $this->cp = CpInfoContainer::getInstance()->getCpById($cp_action_group->cp_id);

        return $this->cp ? $this->cp : false;
    }

    /**
     * @return bool|mixed
     */
    public function getCpAction() {
        if ($this->cp_action) {
            return $this->cp_action;
        }

        if ($this->isEmpty($this->cp_action_id)) {
            return false;
        }

        $this->cp_action = $this->service->getCpActionById($this->cp_action_id);

        return $this->cp_action ? $this->cp_action : false;
    }

    /**
     * @return bool
     */
    public function getCpConcreteAction() {
        if ($this->cp_concrete_action) {
            return $this->cp_concrete_action;
        }

        if ($this->isEmpty($this->cp_action) && !$this->getCpAction()) {
            return false;
        }

        $this->cp_concrete_action = $this->cp_popular_vote_action_manager->getConcreteAction($this->getCpAction());

        return $this->cp_concrete_action ? $this->cp_concrete_action : false;
    }

    /**
     * @return bool
     */
    public function isValidCp() {
        if ($this->getCp() == false) {
            return false;
        }

        return $this->getCp()->status != Cp::STATUS_DRAFT && $this->getCp()->status != Cp::STATUS_SCHEDULE;
    }

    /**
     * @return bool
     */
    public function isValidCpAction() {
        if ($this->getCpAction() == false) {
            return false;
        }

        return $this->getCpAction()->type == CpAction::TYPE_POPULAR_VOTE;
    }

    /**
     * @return bool
     */
    public function isOwnerOfAction() {
        if ($this->getCp() == false) {
            return false;
        }

        return $this->getCp()->brand_id == $this->brandId;
    }

    /**
     * @param $cp_popular_vote_candidate_id
     * @return bool
     */
    public function isValidCandidate($cp_popular_vote_candidate_id) {
        if ($this->cp_popular_vote_action_service->getCpPopularVoteCandidateByIds(
            $this->getCpConcreteAction()->id, $cp_popular_vote_candidate_id
        )) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function validate() {
        if (!$this->isValidCp()) {
            return false;
        }

        if (!$this->isValidCpAction()) {
            return false;
        }

        if (!$this->isOwnerOfAction()) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getCpActionInfo() {
        return array(
            'cp_id' => $this->getCp()->id,
            'action_id' => $this->cp_action_id
        );
    }
}
