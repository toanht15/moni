<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_toggle_comment_user_relation_status extends BrandcoPOSTActionBase {

    protected $ContainerName = 'comment_list';
    protected $AllowContent = array('JSON');

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);
    public $CsrfProtect = true;

    public function validate() {
        if (!$this->isLoginAdmin()) {
            return false;
        }

        return true;
    }

    public function doAction() {
        $comment_user_relation_id = $this->POST['cu_relation_id'];
        $comment_user_relation_status = $this->POST['status'];

        /** @var CommentUserService $comment_user_service */
        $comment_user_service = $this->getService('CommentUserService');

        $comment_user_relation = $comment_user_service->getCommentUserRelationById($comment_user_relation_id);
        if (Util::isNullOrEmpty($comment_user_relation)) {
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $comment_user_relation->status = $comment_user_relation_status;
        $comment_user_service->updateCommentUserRelation($comment_user_relation);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
