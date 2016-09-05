<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpTwitterFollowActionService extends aafwServiceBase {
    protected $cp_twitter_follow_actions;

    public function __construct() {
        $this->cp_twitter_follow_actions = $this->getModel('CpTwitterFollowActions');
    }

    public function getCpTwitterFollowAction($cp_action_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id
        );

        return $this->cp_twitter_follow_actions->findOne($filter);
    }
}
