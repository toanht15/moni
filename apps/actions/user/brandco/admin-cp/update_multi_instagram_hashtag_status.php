<?php
AAFW::import('jp.aainc.classes.brandco.cp.CpInstagramHashtagManagerActionBase');

class update_multi_instagram_hashtag_status extends CpInstagramHashtagManagerActionBase {
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => 'instagram_hashtags/{action_id}'
    );

    public function beforeValidate() {
        $this->instagram_hashtag_approval_status = $this->POST['multi_instagram_hashtag_approval_status'];
    }

    public function doAction() {
        foreach($this->POST['instagram_hashtag_user_post_ids'] as $instagram_hashtag_user_post_id) {
            try {
                $this->updateInstagramHashtagCampaign($instagram_hashtag_user_post_id);
            } catch (Exception $e) {
                $this->logger->error('update_multi_instagram_hashtag_status@doAction Error: ' . $e);
                return 'redirect: ' . Util::rewriteUrl('admin-cp', 'instagram_hashtags', array($this->POST['action_id']), array('mid' => 'failed'));
            }
        }

        return 'redirect: ' . Util::rewriteUrl('admin-cp', 'instagram_hashtags', array($this->POST['action_id']), array('mid' => 'updated'));
    }
}