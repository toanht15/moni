<?php
AAFW::import('jp.aainc.classes.brandco.cp.CpTweetManagerActionBase');

class update_multi_tweet_posts extends CpTweetManagerActionBase {
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => 'tweet_posts/{action_id}'
    );

    public function beforeValidate() {
        $this->setBrandSession('tempTweetPostSession', null);
        $this->tweet_approval_status = isset($this->POST['multi_tweet_approval_status']) ? $this->POST['multi_tweet_approval_status'] : TweetMessage::APPROVAL_STATUS_REJECT;
    }

    public function doAction() {
        $temp_session_data = array(
            'page' => $this->POST['page'],
            'tweet_status' => $this->POST['tweet_status'],
            'approval_status' => $this->POST['approval_status'],
            'order_kind' => $this->POST['order_kind'],
            'order_type' => $this->POST['order_type']
        );
        $this->setBrandSession('tempTweetPostSession', $temp_session_data);

        foreach($this->POST['tweet_message_ids'] as $tweet_message_id) {
            try {
                $this->updateTweetCampaign($tweet_message_id);
            } catch (Exception $e) {
                $this->logger->error('update_multi_photo_status@doAction Error: ' . $e);
                return 'redirect: ' . Util::rewriteUrl('admin-cp', 'tweet_posts', array($this->POST['action_id']), array('mid' => 'failed'));
            }
        }

        return 'redirect: ' . Util::rewriteUrl('admin-cp', 'tweet_posts', array($this->POST['action_id']), array('mid' => 'updated'));
    }
}