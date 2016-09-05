<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.TweetMessage');
AAFW::import('jp.aainc.classes.entities.TweetPhoto');

class TweetMessageService extends aafwServiceBase {
    protected $tweet_messages;
    protected $tweet_photos;

    public function __construct() {
        $this->tweet_messages   = $this->getModel('TweetMessages');
        $this->tweet_photos     = $this->getModel('TweetPhotos');
    }

    /**
     * @param $tweet_message_id
     * @return mixed
     */
    public function getTweetMessageById($tweet_message_id) {
        $filter = array(
            'id' => $tweet_message_id,
        );
        return $this->tweet_messages->findOne($filter);
    }

    /**
     * @param $cp_user_id
     * @param $cp_tweet_action_id
     * @return mixed
     */
    public function getTweetMessageByCpUserId($cp_user_id, $cp_tweet_action_id) {
        if (!$cp_user_id) return null;
        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_id,
                'cp_tweet_action_id' => $cp_tweet_action_id
            ),
        );
        return $this->tweet_messages->findOne($filter);
    }

    /**
     * @param $tweet_message_data
     * @return mixed
     */
    public function updateTweetMessage($tweet_message_data) {
        $tweet_message = $this->getTweetMessageByCpUserId($tweet_message_data['cp_user_id'], $tweet_message_data['cp_tweet_action_id']);
        if (!$tweet_message) return null;
        $tweet_message->tweet_text          = $tweet_message_data['tweet_text'] ? : $tweet_message->tweet_text;
        $tweet_message->tweet_content_url   = $tweet_message_data['tweet_content_url'] ? : $tweet_message->tweet_content_url;
        $tweet_message->has_photo           = $tweet_message_data['has_photo'] ? : $tweet_message->has_photo;
        $tweet_message->skipped             = $tweet_message_data['skipped'] ? : $tweet_message->skipped;
        $tweet_message->approval_status     = $tweet_message_data['approval_status'] ? : $tweet_message->approval_status;

        return $this->saveTweetMessageData($tweet_message);
    }

    /**
     * @param $cp_user_id
     * @param $cp_tweet_action_id
     * @return mixed
     */
    public function createDefaultTweetMessage($cp_user_id, $cp_tweet_action_id) {
        $tweet_message                          = $this->createEmptyTweetMessageData();
        $tweet_message->cp_user_id              = $cp_user_id;
        $tweet_message->cp_tweet_action_id      = $cp_tweet_action_id;
        return $this->saveTweetMessageData($tweet_message);
    }

    public function createEmptyTweetMessageData() {
        return $this->tweet_messages->createEmptyObject();
    }

    public function saveTweetMessageData($tweet_message_data) {
        return $this->tweet_messages->save($tweet_message_data);
    }

    /**
     * @param $tweet_message_id
     */
    public function deleteTweetMessage($tweet_message_id) {
        $tweet_message = $this->getTweetMessageById($tweet_message_id);
        if ($tweet_message) {
            $this->tweet_messages->delete($tweet_message);
        }
    }

    /**
     * @param $tweet_message_id
     * @return null
     */
    public function getTweetPhotos($tweet_message_id) {
        if (!$tweet_message_id) return null;
        $filter = array(
            'conditions' => array(
                'tweet_message_id' => $tweet_message_id,
            ),
        );
        return $this->tweet_photos->find($filter);
    }

    public function createEmptyTweetPhotoData() {
        return $this->tweet_photos->createEmptyObject();
    }

    public function saveTweetPhotoData($tweet_photo_data) {
        return $this->tweet_photos->save($tweet_photo_data);
    }

    /**
     * @param $tweet_message_id
     * @param $image_url
     * @return mixed
     */
    public function createTweetPhoto($tweet_message_id, $image_url) {
        $tweet_photo                    = $this->createEmptyTweetPhotoData();
        $tweet_photo->tweet_message_id  = $tweet_message_id;
        $tweet_photo->image_url         = $image_url;
        return $this->saveTweetPhotoData($tweet_photo);
    }

    public function deleteTweetPhotos($tweet_message_id) {
        $tweet_photo = $this->getTweetPhotos($tweet_message_id);
        foreach ($tweet_photo as $element) {
            $this->tweet_photos->delete($element);
        }
    }
    /**
     * @param $tweet_action_id
     * @return null
     */
    public function getTweetMessagesByTweetActionId($tweet_action_id) {
        if (!$tweet_action_id) {
            return null;
        }
        return $this->tweet_messages->find(array("cp_tweet_action_id" => $tweet_action_id));
    }

    public function deletePhysicalTweetMessageAndPhotoByTweetActionId($tweet_action_id) {
        if (!$tweet_action_id) {
            return;
        }

        $messages = $this->getTweetMessagesByTweetActionId($tweet_action_id);
        if (!$messages) {
            return;
        }

        foreach ($messages as $message) {
            if (!$message || !$message->id) {
                throw new Exception("TweetMessageService#deletePhysicalTweetMessageAndPhotoByTweetActionId null message");
            }
            $photos = $this->getTweetPhotos($message->id);
            if ($photos) {
                foreach ($photos as $photo) {
                    if ($photo && $photo->id) {
                        $this->tweet_photos->deletePhysical($photo);
                    }
                }
            }

            $this->tweet_messages->deletePhysical($message);
        }
    }

    public function deletePhysicalTweetMessageAndPhotoByTweetActionIdAndCpUserId($tweet_action_id, $cp_user_id) {
        if (!$tweet_action_id || !$cp_user_id) {
            return;
        }

        $messages = $this->tweet_messages->find(array("cp_tweet_action_id" => $tweet_action_id, "cp_user_id" => $cp_user_id));
        if (!$messages) {
            return;
        }

        foreach ($messages as $message) {
            if (!$message || !$message->id) {
                throw new Exception("TweetMessageService#deletePhysicalTweetMessageAndPhotoByTweetActionIdAndCpUserId null message");
            }
            $photos = $this->getTweetPhotos($message->id);

            if ($photos) {
                foreach ($photos as $photo) {
                    if ($photo && $photo->id) {
                        $this->tweet_photos->deletePhysical($photo);
                    }
                }
            }

            $this->tweet_messages->deletePhysical($message);
        }
    }


    /**
     * ツイート内容を取得する
     * @param $cp_user_ids
     * @param $cp_tweet_action_id
     * @return mixed
     */
    public function getTweetMessagesByCpUserListAndConcreteActionId($cp_user_ids, $cp_tweet_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_ids,
                'cp_tweet_action_id' => $cp_tweet_action_id,
            ),
        );
        return $this->tweet_messages->find($filter);
    }

    public function getTweetPhotosByTweetMessageList($tweet_message_ids) {
        $filter = array(
            'conditions' => array(
                'tweet_message_id' => $tweet_message_ids,
            ),
        );
        return $this->tweet_photos->find($filter);
    }

    public function getTweetContentByCpUserListAndConcreteActionId($cp_user_id, $cp_tweet_action_id, $tweet_fixed_text) {
        if (!$cp_user_id) return;
        $result = array();
        $tweet_message_array = array();
        $tweet_photo_array = array();

        $tweet_message_ids = array();
        $tweet_message_list = $this->getTweetMessagesByCpUserListAndConcreteActionId($cp_user_id, $cp_tweet_action_id);
        foreach ($tweet_message_list as $element) {
            if (!$element->skipped) {
                if ($element->tweet_content_url == '') {
                    $tweet_message_array[$element->cp_user_id] = array();
                    continue;
                }
                $tweet_message_array[$element->cp_user_id]['tweet_text'] = $element->tweet_text . $tweet_fixed_text;
                $tweet_message_array[$element->cp_user_id]['tweet_content_url'] = $element->tweet_content_url;
            }
            if ($element->has_photo) {
                $tweet_message_array[$element->cp_user_id]['tweet_message_id'] = $element->id;
                $tweet_message_ids[] = $element->id;
            }
            $tweet_message_array[$element->cp_user_id]['tweet_status'] = TweetMessage::$tweet_statuses[$element->skipped];
        }
        $result['tweet_message'] = $tweet_message_array;
        if (!empty($tweet_message_ids)) {
            $tweet_photo_list = $this->getTweetPhotosByTweetMessageList($tweet_message_ids);
            foreach ($tweet_photo_list as $tweet_photo) {
                $tweet_photo_array[$tweet_photo->tweet_message_id][] = $tweet_photo->image_url;
            }
            $result['tweet_photo'] = $tweet_photo_array;
        }
        return $result;
    }

    /**
     * @param $tweet_action_ids
     * @param $params
     * @return mixed
     */
    public function countTweetPostsByActionIds($tweet_action_ids, $params) {
        $filter = array(
            'cp_tweet_action_id' => $tweet_action_ids,
            'tweet_content_url:<>' => ''
        );

        if (isset($params['approval_status'])) {
            $filter['approval_status'] = $params['approval_status'];
        }

        if (isset($params['tweet_status'])) {
            $filter['tweet_status'] = $params['tweet_status'];
        }

        return $this->tweet_messages->count($filter);
    }

    /**
     * @param $cp_tweet_action_ids
     * @param int $page
     * @param int $limit
     * @param null $order
     * @param null $params
     * @return mixed
     */
    public function getTweetMessages($cp_tweet_action_ids, $page = 1, $limit = 20, $order = null, $params = null) {
        $filter = array(
            'conditions' => array(
                'cp_tweet_action_id' => $cp_tweet_action_ids,
                'tweet_content_url:<>' => ''
            ),
            'pager' => array(
                'page' => $page,
                'count' => $limit
            ),
            'order' => $order
        );

        if (isset($params['approval_status'])) {
            $filter['conditions']['approval_status'] = $params['approval_status'];
        }

        if (isset($params['tweet_status'])) {
            $filter['conditions']['tweet_status'] = $params['tweet_status'];
        }

        return $this->tweet_messages->find($filter);
    }
}