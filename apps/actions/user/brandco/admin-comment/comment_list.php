<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.CommentPluginValidator');
AAFW::import('jp.aainc.classes.services.CommentUserService');

class comment_list extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);
    public $NeedAdminLogin = true;

    private $comment_plugin;

    public function validate() {
        $comment_plugin_id = $this->GET['exts'][0];

        if (Util::isNullOrEmpty($comment_plugin_id)) {
            return true;
        }

        $comment_plugin_validator = new CommentPluginValidator($comment_plugin_id, $this->getBrand()->id);
        $comment_plugin_validator->validate();

        if (!$comment_plugin_validator->isValid()) {
            return false;
        }

        $this->comment_plugin = $comment_plugin_validator->getCommentPlugin();
        return true;
    }

    public function doAction() {

        if (!Util::isNullOrEmpty($this->comment_plugin)) {
            $this->Data['comment_plugin'] = $this->comment_plugin;
        }

        $this->Data['page_limit'] = CommentUserService::DISPLAY_20_ITEMS;
        $this->Data['status'] = CommentUserRelation::COMMENT_USER_RELATION_STATUS_ALL;
        $this->Data['note_status'] = CommentUserRelation::NOTE_STATUS_ALL;
        $this->Data['sns_share']   = CommentUserRelation::SNS_SHARE_ALL;
        $this->Data['keyword_type'] = 1;
        $this->Data['cur_form_status'] = CommentUserRelation::COMMENT_USER_RELATION_STATUS_APPROVED;

        return 'user/brandco/admin-comment/comment_list.php';
    }
}