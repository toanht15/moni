<?php
AAFW::import('jp.aainc.classes.services.base.CommentPluginActionBaseService');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.CommentPluginValidator');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class comment extends BrandcoPOSTActionBase {

    use CommentPluginActionBaseService;

    protected $ContainerName = 'page';
    protected $AllowContent = array('JSON');

    protected $ValidatorDefinition = array(
        'comment_text' => array(
            'required' => true,
            'type' => 'str',
            'length' => 5000,
        ),
        'nickname' => array(
            'required' => true,
            'type' => 'str',
            'length' => 40
        )
    );

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);
    public $CsrfProtect = true;

    private $nickname;
    private $request_url;
    private $comment_text;

    public function doThisFirst() {
        $this->initService();
        $this->comment_plugin_id = $this->POST['comment_plugin_id'];
        $this->request_url = $this->POST['request_url'];
        $this->nickname = $this->POST['nickname'];
        $this->comment_text = $this->comment_user_service->trimText($this->POST['comment_text']);

        if (!$this->isLogin()) {
            unset($this->ValidatorDefinition['nickname']);
        }

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
                'nickname' => $this->nickname,
                'object_type' => CommentUserRelation::OBJECT_TYPE_COMMENT,
                'comment_text' => $this->comment_text,
                'comment_plugin_id' => $this->comment_plugin_id,
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
        $comment_user_transaction = aafwEntityStoreFactory::create('CommentUsers');

        try {
            $comment_user_transaction->begin();

            /** @var CommentPluginService $comment_plugin_service */
            $comment_plugin_service = $this->getService('CommentPluginService');

            $static_html_entry_url = $comment_plugin_service->getShareUrl($this->comment_plugin, $this->request_url);
            $comment_plugin_action = $comment_plugin_service->getCommentPluginActionByCommentPluginId($this->comment_plugin->id);
            $comment_concrete_action = $comment_plugin_service->getCommentFreeTextActionByCommentPluginActionId($comment_plugin_action->id);
            $text_content = $this->comment_user_service->getTextContent($this->comment_text);

            $comment_data = array(
                'comment_plugin_id' => $this->comment_plugin->id,
                'comment_action_id' => $comment_concrete_action->id,
                'comment_text' => $this->comment_text,
                'text' => $text_content
            );
            $comment_user = $this->comment_user_service->createCommentUser($comment_data);

            $comment_user_relation_data = array(
                'user_id' => $this->getUserInfo()->id,
                'object_id' => $comment_user->id,
                'object_type' => CommentUserRelation::OBJECT_TYPE_COMMENT,
                'request_url' => $this->request_url
            );
            $comment_user_relation = $this->comment_user_service->createCommentUserRelation($comment_user_relation_data);

            $this->comment_user_service->createCommentUserShares($comment_user_relation->id, $social_media_ids);

            foreach ($social_media_ids as $social_media_id) {
                $this->createMultiPostSnsQueues($comment_user_relation->id, $this->comment_text, $social_media_id, $static_html_entry_url);
            }

            /** @var UserPublicProfileInfoService $user_public_profile_info */
            $public_profile_info_service = $this->getService('UserPublicProfileInfoService');
            $nickname = !Util::isNullOrEmpty($this->nickname) ? $this->nickname : $this->getUserInfo()->name;
            $public_user_info = $public_profile_info_service->createPublicProfileInfo($this->getUserInfo()->id, $nickname);

            $comment_data = $this->getCommonData($comment_user->id, CommentUserRelation::OBJECT_TYPE_COMMENT);
            $comment_data['original_text'] = $this->comment_text;
            $comment_data['comment_text'] = $this->comment_user_service->cutTextByLine($this->comment_text, true, 'もっとみる');

            $response_data = array(
                'nickname' => $public_user_info->nickname,
                'comment' => $comment_data
            );

            $comment_user_transaction->commit();
            $json_data = $this->createAjaxResponse("ok", $response_data);
        } catch (Exception $e) {
            $comment_user_transaction->rollback();
            $json_data = $this->createAjaxResponse("ng");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
