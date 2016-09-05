<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.RetweetMessage');

class RetweetMessageService extends aafwServiceBase {
    protected $retweet_messages;

    public function __construct() {
        $this->retweet_messages   = $this->getModel('RetweetMessages');
    }

    /**
     * @param $retweet_message_id
     * @return mixed
     */
    public function getRetweetMessageById($retweet_message_id) {
        $filter = array(
            'id' => $retweet_message_id,
        );
        return $this->retweet_messages->findOne($filter);
    }

    /**
     * @param $cp_user_id
     * @param $cp_retweet_action_id
     * @return mixed
     */
    public function getRetweetMessageByCpUserId($cp_user_id, $cp_retweet_action_id) {
        if (!$cp_user_id) return null;
        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_id,
                'cp_retweet_action_id' => $cp_retweet_action_id
            ),
        );
        return $this->retweet_messages->findOne($filter);
    }

    /**
     * @param $retweet_message_data
     * @return mixed
     */
    public function updateRetweetMessage($retweet_message_data) {
        $retweet_message = $this->getRetweetMessageByCpUserId($retweet_message_data['cp_user_id'], $retweet_message_data['cp_retweet_action_id']);
        if (!$retweet_message) return null;
        $retweet_message->retweeted          = $retweet_message_data['retweeted'] ? : $retweet_message->retweeted;
        $retweet_message->skipped            = $retweet_message_data['skipped'] ? : $retweet_message->skipped;
        return $this->saveRetweetMessageData($retweet_message);
    }

    /**
     * @param $cp_user_id
     * @param $cp_retweet_action_id
     * @return mixed
     */
    public function createDefaultRetweetMessage($cp_user_id, $cp_retweet_action_id) {
        $retweet_message                          = $this->createEmptyRetweetMessageData();
        $retweet_message->cp_user_id              = $cp_user_id;
        $retweet_message->cp_retweet_action_id    = $cp_retweet_action_id;
        $retweet_message->retweeted               = CpRetweetAction::NOT_POST_RETWEET;
        $retweet_message->skipped                 = CpRetweetAction::NOT_SKIP;
        return $this->saveRetweetMessageData($retweet_message);
    }

    public function createEmptyRetweetMessageData() {
        return $this->retweet_messages->createEmptyObject();
    }

    public function saveRetweetMessageData($retweet_message_data) {
        return $this->retweet_messages->save($retweet_message_data);
    }

    /**
     * @param $retweet_message_id
     */
    public function deleteRetweetMessage($retweet_message_id) {
        $retweet_message = $this->getRetweetMessageById($retweet_message_id);
        if ($retweet_message) {
            $this->retweet_messages->delete($retweet_message);
        }
    }

    /**
     * @param $cp_retweet_action_id
     * @return mixed
     */
    public function getRetweetMessagesByCpRetweetActionId($cp_retweet_action_id) {
        if (!$cp_retweet_action_id) return null;

        return $this->retweet_messages->find(array('cp_retweet_action_id' => $cp_retweet_action_id));
    }

    /**
     * @param $cp_retweet_action_id
     * @param $cp_user_ids
     * @return null
     */
    public function getRetweetMessageByCpRetweetActionIdAndCpUserIds($cp_retweet_action_id, $cp_user_ids) {
        if (!$cp_retweet_action_id || !$cp_user_ids) return null;

        return $this->retweet_messages->find(array('cp_retweet_action_id' => $cp_retweet_action_id, 'cp_user_id' => $cp_user_ids));
    }

    /**
     * @param $cp_retweet_action_id
     * @throws Exception
     */
    public function deletePhysicalRetweetMessagesByCpRetweetActionId($cp_retweet_action_id) {
        if (!$cp_retweet_action_id) return;

        $retweet_message = $this->getRetweetMessagesByCpRetweetActionId($cp_retweet_action_id);
        if (!$retweet_message) return;

        foreach ($retweet_message as $element) {
            if (!$element || !$element->id) {
                throw new Exception("RetweetMessageService#deletePhysicalRetweetMessagesByCpRetweetActionId null message");
            }
            $this->retweet_messages->deletePhysical($element);
        }
    }

    public function deletePhysicalRetweetMessagesByCpRetweetActionIdAndCpUserId($cp_retweet_action_id, $cp_user_id) {
        if (!$cp_retweet_action_id) return;

        $retweet_message = $this->retweet_messages->find(array("cp_user_id" => $cp_user_id, "cp_retweet_action_id" => $cp_retweet_action_id));
        if (!$retweet_message) return;

        foreach ($retweet_message as $element) {
            if (!$element || !$element->id) {
                throw new Exception("RetweetMessageService#deletePhysicalRetweetMessagesByCpRetweetActionIdAndCpUserId null message");
            }
            $this->retweet_messages->deletePhysical($element);
        }
    }

    /**
     * @param $cp_retweet_action_id
     * @param $cp_user_ids
     * @return null
     */
    public function getRetweetActionStatuses($cp_retweet_action_id, $cp_user_ids) {
        if (!$cp_retweet_action_id || !$cp_user_ids) return null;

        $retweet_messages = $this->getRetweetMessageByCpRetweetActionIdAndCpUserIds($cp_retweet_action_id, $cp_user_ids);

        $retweet_messages_array = array();
        foreach ($retweet_messages as $retweet_message) {
            $retweet_messages_array[$retweet_message->cp_user_id]['retweet'] = $this->checkRetweetActionStatus($retweet_message);
        }

        return $retweet_messages_array;
    }

    /**
     * @param $retweet_message
     * @return null|string
     */
    public function checkRetweetActionStatus($retweet_message) {
        if (!$retweet_message) return null;

        if ($retweet_message->skipped == CpRetweetAction::SKIPPED) {
            return 'スキップ';
        } else {
            if ($retweet_message->retweeted == CpRetweetAction::CONNECT_AND_POSTED_RETWEET) {
                return '連携後リツイート';
            } elseif($retweet_message->retweeted == CpRetweetAction::POSTED_RETWEET) {
                return 'リツイート';
            } elseif($retweet_message->retweeted == CpRetweetAction::POST_RETWEET) {
                return '連携';
            } else {
                return '離脱';
            }
        }
    }
}