<?php
AAFW::import('jp.aainc.classes.services.base.CommentPluginActionBaseService');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.CommentPluginValidator');

class like extends BrandcoPOSTActionBase {

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
        $comment_user_relation_id = $this->POST['cu_relation_id'];
        $comment_user_transaction = aafwEntityStoreFactory::create('CommentUsers');

        $comment_data = array(
            'comment_user_relation_id' => $comment_user_relation_id,
            'user_id' => $this->getUserInfo()->id
        );

        try {
            $comment_user_transaction->begin();

            $this->comment_user_service->updateCommentUserLike($comment_data);

            $comment_user_transaction->commit();
            $json_data = $this->createAjaxResponse("ok");
        } catch (Exception $e) {
            $comment_user_transaction->rollback();
            $json_data = $this->createAjaxResponse("ng");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
