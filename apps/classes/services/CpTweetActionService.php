<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.CpTweetAction');

class CpTweetActionService extends aafwServiceBase {
    protected $cp_tweet_actions;

    public function __construct() {
        $this->cp_tweet_actions = $this->getModel('CpTweetActions');
    }

    /**
     * ツイートアクション取得
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpTweetAction($cp_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_action_id' => $cp_action_id,
            ),
        );
        return $this->cp_tweet_actions->findOne($filter);
    }

    /**
     * ツイートアクション取得
     * @param $cp_tweet_action_id
     * @return mixed
     */
    public function getCpTweetActionById($cp_tweet_action_id) {
        $filter = array(
            'id' => $cp_tweet_action_id,
        );
        return $this->cp_tweet_actions->findOne($filter);
    }

    /**
     * @param $cp_action_ids
     * @return array
     */
    public function getCpTweetActionIds($cp_action_ids) {
        $filter = array(
            'conditions' => array(
                'cp_action_id' => $cp_action_ids
            )
        );

        $tweet_actions = $this->cp_tweet_actions->find($filter);
        $cp_tweet_action_ids = array();

        foreach ($tweet_actions as $tweet_action) {
            $cp_tweet_action_ids[] = $tweet_action->id;
        }

        return $cp_tweet_action_ids;
    }

    /**
     * @param $cp_tweet_action
     */
    public function updateCpTweetAction($cp_tweet_action) {
        $this->cp_tweet_actions->save($cp_tweet_action);
    }
}