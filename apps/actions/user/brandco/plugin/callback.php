<?php
AAFW::import('jp.aainc.classes.services.base.CommentPluginActionBaseService');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.CommentPluginValidator');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class callback extends BrandcoGETActionBase {

    use CommentPluginActionBaseService;

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);

    private $comment_data;

    public function doThisFirst() {
        $this->initService();
        $this->comment_data = $this->getBrandSession('commentData');

        $this->setBrandSession('isCmtPluginMode', null);
        $this->setBrandSession('commentData', null);
    }

    public function validate () {
        return true;
    }

    function doAction() {

        $comment_plugin_validator = new CommentPluginValidator($this->comment_data['comment_plugin_id'], $this->getBrand()->id);
        $comment_plugin_validator->validate();

        if (!$comment_plugin_validator->isValid() || !$comment_plugin_validator->isActiveCommentPlugin()) {
            return 'user/brandco/plugin/callback.php';
        }

        $comment_plugin = $comment_plugin_validator->getCommentPlugin();
        $comment_user_transaction = aafwEntityStoreFactory::create('CommentUsers');

        try {
            $comment_user_transaction->begin();

            /** @var CommentPluginService $comment_plugin_service */
            $comment_plugin_service = $this->getService('CommentPluginService');
            $static_html_entry_url = $comment_plugin_service->getShareUrl($comment_plugin, $this->comment_data['page_url']);
            $text_content = $this->comment_user_service->getTextContent($this->comment_data['comment_text']);

            if ($this->comment_data['object_type'] == CommentUserRelation::OBJECT_TYPE_COMMENT) {
                $comment_plugin_action = $comment_plugin_service->getCommentPluginActionByCommentPluginId($comment_plugin->id);
                $comment_concrete_action = $comment_plugin_service->getCommentFreeTextActionByCommentPluginActionId($comment_plugin_action->id);

                $comment_data = array(
                    'comment_plugin_id' => $comment_plugin->id,
                    'comment_action_id' => $comment_concrete_action->id,
                    'comment_text' => $this->comment_data['comment_text'],
                    'text' => $text_content
                );
                $cur_object = $this->comment_user_service->createCommentUser($comment_data);
            } else {
                $comment_data = array(
                    'comment_user_id' => $this->comment_data['comment_user_id'],
                    'comment_text' => $this->comment_data['comment_text'],
                    'text' => $text_content
                );
                $cur_object = $this->comment_user_service->createCommentUserReply($comment_data);
            }

            $comment_user_relation_data = array(
                'user_id' => $this->getUserInfo()->id,
                'object_id' => $cur_object->id,
                'object_type' => $this->comment_data['object_type'],
                'request_url' => $this->comment_data['page_url']
            );
            $comment_user_relation = $this->comment_user_service->createCommentUserRelation($comment_user_relation_data);

            $this->comment_user_service->createCommentUserShares($comment_user_relation->id, $this->comment_data['social_media_ids']);
            // Create Comment User Mention on reply mode (auto skip if this is comment mode)
            $this->comment_user_service->createCommentUserMention($comment_user_relation->id, $this->comment_data['mentioned_object_id']);

            foreach ($this->comment_data['social_media_ids'] as $social_media_id) {
                $this->createMultiPostSnsQueues($comment_user_relation->id, $this->comment_data['comment_text'], $social_media_id, $static_html_entry_url);
            }

            /** @var UserPublicProfileInfoService $user_public_profile_info */
            $public_profile_info_service = $this->getService('UserPublicProfileInfoService');
            $nickname = !Util::isNullOrEmpty($this->comment_data['nickname']) ? $this->comment_data['nickname'] : $this->getUserInfo()->name;
            $public_profile_info_service->createPublicProfileInfo($this->getUserInfo()->id, $nickname);

            $comment_user_transaction->commit();
            $this->Data['anchor_hash'] = $this->getAnchorHash($comment_user_relation->id);
        } catch (Exception $e) {
            $comment_user_transaction->rollback();
            $this->Data['anchor_hash'] = $this->getAnchorHash();
        }

        if (Util::isSmartPhone() && !Util::isNullOrEmpty($this->comment_data['page_url'])) {
            return 'redirect: ' . $this->comment_data['page_url'] . $this->Data['anchor_hash'];
        }

        return 'user/brandco/plugin/callback.php';
    }

    /**
     * @param null $comment_user_relation_id
     * @return string
     */
    public function getAnchorHash($comment_user_relation_id = null) {
        if (Util::isNullOrEmpty($comment_user_relation_id)) {
            return '';
        }

        return CommentUserService::ANCHOR_PREFIX . $comment_user_relation_id;
    }
}
