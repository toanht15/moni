<?php
AAFW::import('jp.aainc.classes.services.base.CommentPluginActionBaseService');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.CommentPluginValidator');

class edit extends BrandcoPOSTActionBase {

    use CommentPluginActionBaseService;

    protected $ContainerName = 'page';
    protected $AllowContent = array('JSON');

    protected $ValidatorDefinition = array(
        'comment_text' => array(
            'required' => true,
            'type' => 'str',
            'length' => 5000,
        )
    );

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);
    public $CsrfProtect = true;

    private $comment_text;

    public function doThisFirst() {
        $this->initService();
        $this->comment_text = $this->comment_user_service->trimText($this->POST['comment_text']);
    }

    public function validate() {

        if (Util::isNullOrEmpty($this->comment_text)) {
            $this->Validator->setError('comment_text', 'NOT_REQUIRED');
            return false;
        }

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
            $text_content = $this->comment_user_service->getTextContent($this->comment_text);

            if ($comment_user_relation->object_type == CommentUserRelation::OBJECT_TYPE_COMMENT) {

                $comment_free_text_user = $this->comment_user_service->getCommentFreeTextUser($comment_user_relation->object_id);
                $comment_free_text_user->text = $text_content;
                $comment_free_text_user->extra_data = $this->comment_user_service->encodeComment($this->comment_text);
                $this->comment_user_service->updateCommentFreeTextUser($comment_free_text_user);

            } else if ($comment_user_relation->object_type == CommentUserRelation::OBJECT_TYPE_REPLY) {

                $cur_object = $this->getCurObject($comment_user_relation->object_id, $comment_user_relation->object_type);
                $cur_object->text = $text_content;
                $cur_object->extra_data = $this->comment_user_service->encodeComment($this->comment_text);
                $this->comment_user_service->updateCommentUserReply($cur_object);

            }

            $response_data = array(
                'comment_text' => $this->comment_text
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
