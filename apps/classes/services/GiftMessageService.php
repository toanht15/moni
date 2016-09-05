<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.GiftMessage');

class GiftMessageService extends aafwServiceBase {

    protected $gift_messages;

    public function __construct() {
        $this->gift_messages = $this->getModel('GiftMessages');
    }

    public function getGiftMessageById($gift_message_id) {
        $filter = array(
            'id' => $gift_message_id
        );
        return $this->gift_messages->findOne($filter);
    }

    /**
     * @param $gift_message_id
     * @param $param_hash
     * @return mixed
     */
    public function getGiftMessageByCode($gift_message_id, $param_hash) {
        if (!$gift_message_id || !$gift_message_id) return null;

        $gift_message = $this->getGiftMessageById($gift_message_id);
        if (!$gift_message) return null;

        return $gift_message->param_hash == $param_hash ? $gift_message : null;
    }

    /**
     * ギフトメッセージの情報を取得する
     * @param $cp_user_id
     * @param $cp_gift_action_id
     * @return mixed
     */
    public function getGiftMessageByCpUserIdAndCpGiftActionId($cp_user_id, $cp_gift_action_id) {
        if (!$cp_user_id) return null;
        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_id,
                'cp_gift_action_id' => $cp_gift_action_id
            ),
        );
        return $this->gift_messages->findOne($filter);
    }

    /**
     * ギフトメッセージの画像を更新する
     * @param $cp_user_id
     * @param $cp_gift_action_id
     * @param $image_url
     * @param $sender_text
     * @param $receiver_text
     * @param $content_text
     * @return mixed
     */
    public function updateGreetingCardImage($cp_user_id, $cp_gift_action_id, $image_url, $sender_text, $receiver_text, $content_text) {
        $gift_message = $this->getGiftMessageByCpUserIdAndCpGiftActionId($cp_user_id, $cp_gift_action_id);

        if (!$gift_message) return null;

        $gift_message->image_url        = $image_url;
        $gift_message->sender_text      = $sender_text;
        $gift_message->receiver_text    = $receiver_text;
        $gift_message->content_text     = $content_text;
        return $this->saveGiftMessageData($gift_message);
    }

    /**
     * ギフトメッセージ送信のステータスを更新する
     * @param $cp_user_id
     * @param $cp_gift_action_id
     * @param int $send_flg
     * @param int $media_type
     * @return mixed
     */
    public function updateGreetingCardSendStatus($cp_user_id, $cp_gift_action_id, $send_flg = 0, $media_type = 0) {
        $gift_message = $this->getGiftMessageByCpUserIdAndCpGiftActionId($cp_user_id, $cp_gift_action_id);

        if (!$gift_message) return null;

        $gift_message->send_flg     = $send_flg;
        $gift_message->media_type   = $media_type;
        return $this->saveGiftMessageData($gift_message);
    }

    /**
     * @param $gift_message_id
     * @param $receiver_user_id
     * @return mixed
     */
    public function updateGreetingCardReceiverStatus($gift_message_id, $receiver_user_id) {
        $gift_message = $this->getGiftMessageById($gift_message_id);

        if (!$gift_message) return null;

        $gift_message->receiver_user_id     = $receiver_user_id;
        return $this->saveGiftMessageData($gift_message);
    }

    /**
     * ギフトメッセージのデフォルト値を作成する
     * @param $cp_user_id
     * @param $cp_gift_action_id
     * @param $coupon_code_id
     * @return mixed
     */
    public function createDefaultGiftMessage($cp_user_id, $cp_gift_action_id, $coupon_code_id) {
        $gift_message                       = $this->createEmptyGiftMessageData();
        $gift_message->cp_user_id           = $cp_user_id;
        $gift_message->cp_gift_action_id    = $cp_gift_action_id;
        $gift_message->coupon_code_id       = $coupon_code_id;
        $gift_message->param_hash           = $this->getUniqueParam($cp_user_id);
        $gift_message->image_url            = '';
        $gift_message->send_flg             = GiftMessage::NOT_SENT;
        $gift_message->media_type           = GiftMessage::MEDIA_TYPE_DEFAULT;
        return $this->saveGiftMessageData($gift_message);
    }

    /**
     * ユーザの作成したギフトメッセージを削除する
     * @param $gift_message_id
     */
    public function deleteGiftMessage($gift_message_id) {
        $gift_message = $this->getGiftMessageById($gift_message_id);
        if ($gift_message) {
            $this->gift_messages->delete($gift_message);
        }
    }

    public function createEmptyGiftMessageData() {
        return $this->gift_messages->createEmptyObject();
    }

    public function saveGiftMessageData($gift_message_data) {
        return $this->gift_messages->save($gift_message_data);
    }

    private function getUniqueParam($cp_user_id) {
        return sha1(uniqid() . mt_rand() . $cp_user_id);
    }

    /**
     * @param $cp_gift_action_id
     */
    public function deletePhysicalGiftMessageByCpGiftActionId($cp_gift_action_id) {
        if (!$cp_gift_action_id) {
            return;
        }
        $gift_messages = $this->gift_messages->find(array("cp_gift_action_id" => $cp_gift_action_id));
        if (!$gift_messages) {
            return;
        }
        $coupon_code_manager = new CpCouponActionManager();
        foreach ($gift_messages as $gift_message) {
            $coupon_code_manager->restoreCouponCode($gift_message->coupon_code_id);
            $this->gift_messages->deletePhysical($gift_message);
        }
    }

    public function deletePhysicalGiftMessageByCpGiftActionIdAndCpUserId($cp_gift_action_id, $cp_user_id) {
        if (!$cp_gift_action_id || !$cp_user_id) {
            return;
        }
        $gift_messages = $this->gift_messages->find(array("cp_gift_action_id" => $cp_gift_action_id, "cp_user_id" => $cp_user_id));
        if (!$gift_messages) {
            return;
        }
        $coupon_code_manager = new CpCouponActionManager();
        foreach ($gift_messages as $gift_message) {
            $coupon_code_manager->restoreCouponCode($gift_message->coupon_code_id);
            $this->gift_messages->deletePhysical($gift_message);
        }
    }
}

