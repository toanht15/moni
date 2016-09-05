<?php
AAFW::import('jp.aainc.classes.brandco.cp.CpPhotoManagerActionBase');

class edit_photo extends CpPhotoManagerActionBase {
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => 'photo_campaign/{dtl_photo_action_id}'
    );

    public function beforeValidate() {
        $this->photo_top_status = isset($this->POST['dtl_photo_top_status']) ? $this->POST['dtl_photo_top_status'] : PhotoEntry::TOP_STATUS_HIDDEN;
        $this->photo_approval_status = $this->photo_top_status == PhotoEntry::TOP_STATUS_AVAILABLE ? PhotoUser::APPROVAL_STATUS_APPROVE : $this->POST['dtl_photo_approval_status'];
    }

    public function doAction() {
        try {
            $this->updatePhotoCampaign($this->POST['dtl_photo_user_id']);
        } catch (Exception $e) {
            $this->logger->error('edit_photo@doAction Error: ' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'photo_campaign', array($this->POST['dtl_photo_action_id']), array('mid' => 'failed'));
        }

        return 'redirect: ' . Util::rewriteUrl('admin-cp', 'photo_campaign', array($this->POST['dtl_photo_action_id']), array('mid' => 'updated'));
    }
}