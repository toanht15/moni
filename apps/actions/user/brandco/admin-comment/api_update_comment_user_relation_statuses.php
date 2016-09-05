<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_update_comment_user_relation_statuses extends BrandcoPOSTActionBase {

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
        $comment_user_relation_ids = $this->POST['cur_form_ids'];
        $comment_user_relation_status = $this->POST['cur_form_status'];

        /** @var CommentUserService $comment_user_service */
        $comment_user_service = $this->getService('CommentUserService');

        $comment_user_relations = $comment_user_service->getCommentUserRelationByIds($comment_user_relation_ids);
        if (Util::isNullOrEmpty($comment_user_relations)) {
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        foreach ($comment_user_relations as $comment_user_relation) {
            if ($comment_user_relation->isRejected() && $comment_user_relation->isDiscard()) {
                continue;
            }

            $comment_user_relation->status = $comment_user_relation_status;
            $comment_user_service->updateCommentUserRelation($comment_user_relation);
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
