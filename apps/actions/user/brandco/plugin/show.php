<?php
AAFW::import('jp.aainc.classes.services.base.CommentPluginActionBaseService');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.CommentPluginValidator');

class show extends BrandcoPOSTActionBase {

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
        // Fetching comment user data
        $comment_user_relation_id = $this->POST['cu_relation_id'];
        $cur_transaction = aafwEntityStoreFactory::create('CommentUserRelations');

        try {
            $cur_transaction->begin();

            $this->comment_user_service->deleteCommentUserHiddenLog($comment_user_relation_id, $this->getUserInfo()->id);

            $comment_user_relation = $this->comment_user_service->getCommentUserRelationById($comment_user_relation_id);
            $comment_data = $this->getCommonData($comment_user_relation->object_id, $comment_user_relation->object_type);

            if ($comment_user_relation->object_type == CommentUserRelation::OBJECT_TYPE_COMMENT) {
                $comment_free_text_user = $this->comment_user_service->getCommentFreeTextUser($comment_user_relation->object_id);
                $comment_text = $this->comment_user_service->decodeComment($comment_free_text_user->extra_data);
                $comment_data['comment_text'] = $this->comment_user_service->cutTextByLine($comment_text, true, 'もっとみる');
            } else {
                $cur_object = $this->getCurObject($comment_user_relation->object_id, $comment_user_relation->object_type);
                $comment_text = $this->comment_user_service->decodeComment($cur_object->extra_data);
                $comment_data['comment_text'] = $this->comment_user_service->cutTextByLine($comment_text, true, 'もっとみる');
            }

            $response_data = array(
                'comment' => $comment_data
            );

            $cur_transaction->commit();
            $json_data = $this->createAjaxResponse("ok", $response_data);
        } catch (Exception $e) {
            $cur_transaction->rollback();
            $json_data = $this->createAjaxResponse("ng");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
