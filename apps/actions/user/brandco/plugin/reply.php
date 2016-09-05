<?php
AAFW::import('jp.aainc.classes.services.base.CommentPluginActionBaseService');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.CommentPluginValidator');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class reply extends BrandcoPOSTActionBase {

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

    private $request_url;
    private $comment_text;
    private $comment_user;
    private $mentioned_object_id;

    public function doThisFirst() {
        $this->initService();

        $this->mentioned_object_id = $this->POST['mentioned_object_id'];
        $this->comment_text = $this->comment_user_service->trimText($this->POST['comment_text']);
        $this->request_url = $this->POST['request_url'];

        $this->setBrandSession('commentData', null);
        $this->setSession('loginRedirectUrl', null);
    }

    public function beforeValidate () {
        if (!$this->isLogin() && !Util::isNullOrEmpty($this->request_url) && Util::isSmartPhone()) {
            $this->setSession('loginRedirectUrl', $this->request_url);
        }
    }

    public function validate() {

        if (Util::isNullOrEmpty($this->comment_text)) {
            $this->Validator->setError('comment_text', 'NOT_REQUIRED');
            return false;
        }

        // Fetching comment user data
        $comment_user_relation_id = $this->POST['cu_relation_id'];
        $comment_user_relation = $this->comment_user_service->getCommentUserRelationById($comment_user_relation_id);

        if ($comment_user_relation->object_type == CommentUserRelation::OBJECT_TYPE_REPLY) {
            return false;
        }

        $this->comment_user = $this->getCurObject($comment_user_relation->object_id, $comment_user_relation->object_type);
        $this->comment_plugin_id = $this->comment_user->comment_plugin_id;

        if (!$this->comment_user) {
            return false;
        }

        $comment_plugin_validator = new CommentPluginValidator($this->comment_plugin_id, $this->getBrand()->id);
        $comment_plugin_validator->validate();

        if (!$comment_plugin_validator->isValid()) {
            return false;
        }

        if (!$comment_plugin_validator->isActiveCommentPlugin()) {
            return false;
        }


        if (!$this->isLogin()) {
            $comment_data = array(
                'page_url' => $this->getLoginRedirectUrl(),
                'object_type' => CommentUserRelation::OBJECT_TYPE_REPLY,
                'comment_text' => $this->comment_text,
                'comment_user_id' => $this->comment_user->id,
                'comment_plugin_id' => $this->comment_plugin_id,
                'mentioned_object_id' => $this->mentioned_object_id
            );
            $this->setBrandSession('commentData', $comment_data);

            $form_data = array(
                'redirect_url' => Util::rewriteUrl('my', 'login', array(), array('display' => 'popup')),
                'device' => Util::isSmartPhone() ? 'sp' : 'pc'
            );
            $this->setFormData($form_data);

            $this->Validator->setError('auth_error', '1');
            return false;
        }

        $this->comment_plugin = $comment_plugin_validator->getCommentPlugin();
        return true;
    }

    public function doAction() {
        $social_media_ids = $this->POST['social_media_ids'];

        $comment_user_reply_transaction = aafwEntityStoreFactory::create('CommentUserReplies');

        try {
            $comment_user_reply_transaction->begin();

            /** @var CommentPluginService $comment_plugin_service */
            $comment_plugin_service = $this->getService('CommentPluginService');
            $static_html_entry_url = $comment_plugin_service->getShareUrl($this->comment_plugin, $this->request_url);
            $text_content = $this->comment_user_service->getTextContent($this->comment_text);

            $comment_data = array(
                'comment_user_id' => $this->comment_user->id,
                'comment_text' => $this->comment_text,
                'text' => $text_content
            );
            $comment_user_reply = $this->comment_user_service->createCommentUserReply($comment_data);

            $comment_user_relation_data = array(
                'user_id' => $this->getUserInfo()->id,
                'object_id' => $comment_user_reply->id,
                'object_type' => CommentUserRelation::OBJECT_TYPE_REPLY,
                'request_url' => $this->request_url
            );
            $comment_user_relation = $this->comment_user_service->createCommentUserRelation($comment_user_relation_data);

            $this->comment_user_service->createCommentUserShares($comment_user_relation->id, $social_media_ids);
            $this->comment_user_service->createCommentUserMention($comment_user_relation->id, $this->mentioned_object_id);

            /** @var UserPublicProfileInfoService $user_public_profile_info */
            $public_profile_info_service = $this->getService('UserPublicProfileInfoService');
            $public_profile_info_service->createPublicProfileInfo($this->getUserInfo()->id, $this->getUserInfo()->name);

            foreach ($social_media_ids as $social_media_id) {
                $this->createMultiPostSnsQueues($comment_user_relation->id, $this->comment_text, $social_media_id, $static_html_entry_url);
            }

            $comment_data = $this->getCommonData($comment_user_reply->id, CommentUserRelation::OBJECT_TYPE_REPLY);
            $comment_data['original_text'] = $this->comment_text;
            $comment_data['comment_text'] = $this->comment_user_service->cutTextByLine($this->comment_text, true, 'もっとみる');

            $response_data = array(
                'reply' => $comment_data
            );

            $comment_user_reply_transaction->commit();
            $json_data = $this->createAjaxResponse("ok", $response_data);
        } catch (Exception $e) {
            $comment_user_reply_transaction->rollback();
            $json_data = $this->createAjaxResponse("ng");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
