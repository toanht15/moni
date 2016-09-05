<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpFacebookLikeActionService extends aafwServiceBase {

    protected $cp_facebook_like_actions;

    public function __construct() {
        $this->cp_facebook_like_actions = $this->getModel('CpFacebookLikeActions');
    }

    public function getCpFacebookLikeAction($cp_action_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id
        );

        return $this->cp_facebook_like_actions->findOne($filter);
    }
}