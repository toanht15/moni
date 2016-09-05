<?php
AAFW::import('jp.aainc.classes.services.base.CommentPluginActionBaseService');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.CommentPluginValidator');

class remove extends BrandcoPOSTActionBase {

    use CommentPluginActionBaseService;

    protected $ContainerName = 'page';
    protected $AllowContent = array('JSON');

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);
    public $CsrfProtect = true;

    public function doThisFirst() {
        $this->initService();
    }

    public function validate() {
        if (!$this->isLogin()) {
            return false;
        }

        return true;
    }

    public function doAction() {
        $cur_transaction = aafwEntityStoreFactory::create('CommentUserRelations');

        // Fetching comment user data
        $comment_user_relation_id = $this->POST['cu_relation_id'];
        $comment_user_relation = $this->comment_user_service->getCommentUserRelationById($comment_user_relation_id);

        if (Util::isNullOrEmpty($comment_user_relation) || $comment_user_relation->user_id != $this->getUserInfo()->id) {
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        try {
            $cur_transaction->begin();

            $comment_user_relation->status = CommentUserRelation::COMMENT_USER_RELATION_STATUS_REJECTED;
            $comment_user_relation->discard_flg = CommentUserRelation::DISCARD_FLG_ON;
            $this->comment_user_service->updateCommentUserRelation($comment_user_relation);

            $cur_transaction->commit();
            $json_data = $this->createAjaxResponse("ok");
        } catch (Exception $e) {
            $cur_transaction->rollback();
            $json_data = $this->createAjaxResponse("ng");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
