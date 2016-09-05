<?php

trait CommentPluginActionBaseService {

    protected $comment_user_service;
    protected $user_service;
    protected $php_parser;
    protected $logger;

    protected $comment_plugin_id;
    protected $comment_plugin;
    protected $cur_user;

    protected $form_data;

    public function initService() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();

        /** @var CommentUserService comment_user_service */
        $this->comment_user_service = $this->getService('CommentUserService');
        /** @var UserService user_service */
        $this->user_service = $this->getService('UserService');
        /** @var PHPParser $php_parser */
        $this->php_parser = new PHPParser();
    }

    /**
     * @return string
     */
    public function getFormURL() {
        $form_data = $this->getFormData();
        $errors = array();

        if (isset($this->Validator) && !Util::isNullOrEmpty($this->Validator)) {
            foreach ($this->Validator->getError() as $key => $value) {
                $errors[$key] = $this->Validator->getMessage($key);
            }
        }

        $json_data = $this->createAjaxResponse('ng', $form_data, $errors);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    /**
     * @return mixed
     */
    public function getFormData() {
        return $this->form_data;
    }

    /**
     * @param array $form_data
     */
    public function setFormData($form_data = array()) {
        $this->form_data = $form_data;
    }

    /**
     * @param $object_id
     * @param $object_type
     * @return array
     */
    public function getCommonData($object_id, $object_type) {
        $data = array();
        $user_info = $this->getUserInfo();

        $comment_user_relation = $this->comment_user_service->getCommentUserRelation($object_id, $object_type);
        $from_user = $this->user_service->getUserPublicInfoByBrandcoUserId($comment_user_relation->user_id);
        $data['from'] = array(
            'id' => $from_user->id,
            'name' => $from_user->name,
            'profile_img_url' => $this->getProfileImgUrl($from_user->profile_image_url)
        );

        $data['id'] = $comment_user_relation->id;
        $data['likes'] = $this->comment_user_service->getLikeData($user_info, $comment_user_relation->id);
        $data['is_owner'] = $this->comment_user_service->isOwner($user_info, $comment_user_relation->user_id) && $this->isLogin();
        $data['is_hidden'] = $this->comment_user_service->isHiddenComment($comment_user_relation->id, $user_info->id);
        $data['created_time'] = $this->getCreatedTime($comment_user_relation->created_at);

        return $data;
    }

    /**
     * @param $created_at
     * @return bool|string
     */
    public function getCreatedTime($created_at) {
        $created_time = new DateTime($created_at);
        $since_created = $created_time->diff(new DateTime());

        if ($since_created->d >= 1) {
            return date('Y/m/d H:i', strtotime($created_at));
        }

        if ($since_created->h >= 1) {
            return $since_created->h . '時間前';
        }

        if ($since_created->i >= 1) {
            return $since_created->i . '分前';
        }

        return 'たった今';
    }

    /**
     * @return array
     */
    public function getUserData() {
        /** @var CommentPluginService $comment_plugin_service */
        $comment_plugin_service = $this->getService('CommentPluginService');
        $has_fb_public_actions = $this->hasFBPublishActions($this->Data['pageStatus']['userInfo'], $this->getBrand()->app_id);
        $share_sns_list = $comment_plugin_service->getUserShareSNSList($this->comment_plugin_id, $this->Data['pageStatus']['userInfo'], $has_fb_public_actions);

        $user = array(
            'id' => $this->getUserInfo()->id,
            'is_login' => $this->isLogin(),
            'name' => $this->getUserInfo()->name,
            'profile_img_url' => $this->getProfileImgUrl($this->getUserInfo()->profile_image_url),
            'share_sns_list' => $share_sns_list
        );

        return $user;
    }

    /**
     * @return mixed
     */
    public function getUserInfo() {
        if (!$this->cur_user) {
            $monipla_user_id = $this->Data['pageStatus']['userInfo']->id;
            $this->cur_user = $this->user_service->getUserPublicInfoByMoniplaUserId($monipla_user_id);
        }

        return $this->cur_user;
    }

    /**
     * @param $profile_img_url
     * @return mixed
     */
    public function getProfileImgUrl($profile_img_url) {
        if (!$profile_img_url) {
            return $this->php_parser->setVersion('/img/base/imgUser1.jpg');
        }

        return $profile_img_url;
    }

    /**
     * @param $object_id
     * @param $object_type
     * @return null
     */
    public function getCurObject($object_id, $object_type) {
        $cur_object = null;

        if ($object_type == CommentUserRelation::OBJECT_TYPE_COMMENT) {
            $cur_object = $this->comment_user_service->getCommentUserById($object_id);
        } elseif ($object_type == CommentUserRelation::OBJECT_TYPE_REPLY) {
            $cur_object = $this->comment_user_service->getCommentUserReplyById($object_id);
        }

        return $cur_object;
    }

    /**
     * TODO need to move to BrandcoActionBaseService
     * @return null|string
     */
    public function getLoginRedirectUrl() {
        $login_redirect_url = $this->getSession('loginRedirectUrl');
        if (!$login_redirect_url) {
            return $login_redirect_url;
        }

        $parsed_url = parse_url($login_redirect_url);

        $mapped_brand_id = Util::getMappedBrandId($parsed_url['host']);
        if ($mapped_brand_id != Util::NOT_MAPPED_BRAND && $this->getBrand()->id != $mapped_brand_id) {
            return Util::getBaseUrl();
        }

        return $login_redirect_url;
    }

    public function createMultiPostSnsQueues($comment_user_relation_id, $comment_text, $social_media_type, $share_url) {
        $app_id = $this->getBrand()->app_id;
        $token_array = $this->getAccessTokenInfo($this->Data['pageStatus']['userInfo'], $social_media_type, $app_id);

        if (!count($token_array)) {
            $this->logger->error('CommentPlugin_comment#getAccessTokenInfo error');
        }

        $multi_post_sns_queue_service = $this->getService('MultiPostSnsQueueService');
        $multi_post_sns_queue = $multi_post_sns_queue_service->createEmptyObject();

        $multi_post_sns_queue->access_token = $token_array['social_media_access_token'];
        $multi_post_sns_queue->access_refresh_token = $token_array['social_media_access_refresh_token'];
        $multi_post_sns_queue->callback_parameter = $comment_user_relation_id;
        $multi_post_sns_queue->social_media_type = $social_media_type;

        $share_text = $this->comment_user_service->parseTextForSnsSharing($comment_text);
        $multi_post_sns_queue->share_long_text = $this->comment_user_service->encodeComment($share_text);
        
        $multi_post_sns_queue->share_url = $share_url;
        $multi_post_sns_queue->callback_function_type = MultiPostSnsQueue::CALLBACK_UPDATE_COMMENT_USER_SHARE;
        $multi_post_sns_queue->social_account_id = $this->getSNSAccountId($this->Data['pageStatus']['userInfo'], SocialAccount::$socialMediaTypeName[$social_media_type]);

        $multi_post_sns_queue_service->update($multi_post_sns_queue);
    }

    /**
     * TODO 共通化
     * モニプラからアクセストークン取得
     * @param $user_info
     * @param $social_media_type
     * @param $app_id
     * @return array
     */
    private function getAccessTokenInfo($user_info, $social_media_type, $app_id) {
        $user_sns_account_manager = new UserSnsAccountManager($user_info, null, $app_id);

        if ($social_media_type == SocialAccount::SOCIAL_MEDIA_FACEBOOK) {
            return $user_sns_account_manager->getSnsAccountInfo($social_media_type, SocialAccount::$socialMediaTypeName[SocialAccount::SOCIAL_MEDIA_FACEBOOK]);
        }elseif ($social_media_type == SocialAccount::SOCIAL_MEDIA_TWITTER){
            return $user_sns_account_manager->getSnsAccountInfo($social_media_type, SocialAccount::$socialMediaTypeName[SocialAccount::SOCIAL_MEDIA_TWITTER]);
        }
    }

    /**
     * Return -1 if user don't have this type of SNS
     * @param $user_info
     * @param $sns_type
     * @return int
     */
    public function getSNSAccountId($user_info, $sns_type) {
        foreach ($user_info->socialAccounts as $social_account) {
            if ($social_account->socialMediaType == $sns_type) {
                return $social_account->socialMediaAccountID;
            }
        }

        return -1;
    }

    /**
     * @param $anchor_link
     * @return mixed
     */
    public function getAnchorId($anchor_link) {
        if (Util::isNullOrEmpty($anchor_link)) {
            return "";
        }

        $res = explode("#cur_id_", $anchor_link);
        return $res[1];
    }
}
