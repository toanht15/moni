<?php
AAFW::import('jp.aainc.classes.brandco.cp.CpPhotoManagerActionBase');

class update_multi_photo_status extends CpPhotoManagerActionBase {
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => 'photo_campaign/{action_id}'
    );

    public function beforeValidate() {
        $this->photo_top_status = isset($this->POST['multi_photo_top_status']) ? $this->POST['multi_photo_top_status'] : PhotoEntry::TOP_STATUS_HIDDEN;
        $this->photo_approval_status = $this->photo_top_status == PhotoEntry::TOP_STATUS_AVAILABLE ? PhotoUser::APPROVAL_STATUS_APPROVE : $this->POST['multi_photo_approval_status'];
    }

    public function doAction() {
        foreach($this->POST['photo_user_ids'] as $photo_user_id) {
            try {
                $this->updatePhotoCampaign($photo_user_id);
            } catch (Exception $e) {
                $this->logger->error('update_multi_photo_status@doAction Error: ' . $e);
                return 'redirect: ' . Util::rewriteUrl('admin-cp', 'photo_campaign', array($this->POST['action_id']), array('mid' => 'failed'));
            }
        }

        return 'redirect: ' . Util::rewriteUrl('admin-cp', 'photo_campaign', array($this->POST['action_id']), array('mid' => 'updated'));
    }
}