<?php
AAFW::import('jp.aainc.classes.services.base.CommentPluginActionBaseService');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.CommentPluginValidator');

class hide extends BrandcoPOSTActionBase {

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

            $this->comment_user_service->createCommentUserHiddenLog($comment_user_relation_id, $this->getUserInfo()->id);

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
