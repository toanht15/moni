<?php
AAFW::import('jp.aainc.classes.brandco.cp.CpInstagramHashtagManagerActionBase');

class edit_instagram_hashtag extends CpInstagramhashtagManagerActionBase {
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => 'instagram_hashtags/{dtl_instagram_hashtaag_action_id}'
    );

    public function beforeValidate() {
        $this->instagram_hashtag_approval_status = $this->POST['dtl_instagram_hashtag_approval_status'];
    }

    public function doAction() {
        try {
            $this->updateInstagramHashtagCampaign($this->POST['dtl_instagram_hashtag_user_post_id']);
        } catch (Exception $e) {
            $this->logger->error('edit_instagram_hashtag@doAction Error: ' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'instagram_hashtags', array($this->POST['dtl_instagram_hashtags_action_id']), array('mid' => 'failed'));
        }

        return 'redirect: ' . Util::rewriteUrl('admin-cp', 'instagram_hashtags', array($this->POST['dtl_instagram_hashtag_action_id']), array('mid' => 'updated'));
    }
}