<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_save_comment_user_relation_note extends BrandcoPOSTActionBase {

    protected $ContainerName = 'comment_list';
    protected $AllowContent = array('JSON');

    protected $ValidatorDefinition = array(
        'note' => array(
            'type' => 'str',
            'length' => 5000,
        )
    );

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
        $comment_user_relation_note = $this->POST['note'];

        /** @var CommentUserService $comment_user_service */
        $comment_user_service = $this->getService('CommentUserService');

        $comment_user_relation = $comment_user_service->getCommentUserRelationById($comment_user_relation_id);
        if (Util::isNullOrEmpty($comment_user_relation)) {
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $comment_user_relation->note = $comment_user_relation_note;
        $comment_user_service->updateCommentUserRelation($comment_user_relation);

        $response_data = array();
        if (Util::isNullOrEmpty($comment_user_relation_note)) {
            $response_data['is_removed'] = "1";
        } else {
            $response_data['is_saved'] = "1";
        }

        $json_data = $this->createAjaxResponse("ok", $response_data);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
