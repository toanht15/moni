<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.vendor.twitter.Twitter');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.entities.CpTweetAction');

/**
 * Class SnsPostShare
 */
class MultiPostSnsShare extends BrandcoBatchBase {

    /** @var PhotoUserService $photo_user_service */
    protected $photo_user_service;

    /** UserSnsAccountManager user_sns_account_manager */
    protected $user_sns_account_manager;

    /** @var MultiPostSnsQueueService $multi_post_sns_queue_service */
    protected $multi_post_sns_queue_service;

    /** @var PhotoUserShareService $photo_user_share_service */
    protected $photo_user_share_service;

    /** @var PopularVoteUserShareService $popular_vote_user_share_service */
    protected $popular_vote_user_share_service;

    /** @var  CommentUserService $comment_user_service */
    protected $comment_user_service;

    public function __construct() {
        parent::__construct();
        $this->multi_post_sns_queue_service = $this->service_factory->create('MultiPostSnsQueueService');
        $this->photo_user_share_service = $this->service_factory->create('PhotoUserShareService');
        $this->popular_vote_user_share_service = $this->service_factory->create('PopularVoteUserShareService');
        $this->comment_user_service = $this->service_factory->create('CommentUserService');
        $this->facebook_api_client = new FacebookApiClient(FacebookApiClient::BRANDCO_MODE_USER);
        $this->photo_user_service = $this->service_factory->create('PhotoUserService');
    }

    function executeProcess() {
        $multi_post_sns_queues = $this->multi_post_sns_queue_service->getMultiPostSnsQueues();

        if (!$multi_post_sns_queues){
            return;
        }

        $multi_post_sns_queue_transaction = aafwEntityStoreFactory::create('MultiPostSnsQueues');

        foreach($multi_post_sns_queues as $multi_post_sns_queue) {
            if (!$multi_post_sns_queue->callback_function_type) {
                throw new Exception('MultiPostSnsShare#executeProcess no callback_function_type.');
            }

            $is_api_success = false;
            $api_result_json = '';

            try {
                $multi_post_sns_queue_transaction->begin();

                // ロック
                if ($this->multi_post_sns_queue_service->getMultiPostSnsQueueByIdForUpdate($multi_post_sns_queue->id)) {

                    if ($multi_post_sns_queue->callback_function_type == MultiPostSnsQueue::CALLBACK_UPDATE_COMMENT_USER_SHARE) {
                        $multi_post_sns_queue->shared_text = $this->comment_user_service->decodeComment($multi_post_sns_queue->share_long_text);
                    } else {
                        $multi_post_sns_queue->shared_text = $multi_post_sns_queue->share_text;
                    }

                    // 各SNSのシェア処理
                    if ($multi_post_sns_queue->social_media_type == SocialAccount::SOCIAL_MEDIA_FACEBOOK) {
                        $api_result = $this->fbShare($multi_post_sns_queue);
                        $api_result_json = json_encode($api_result);
                        if ($api_result['id']) $is_api_success = true;

                    }elseif($multi_post_sns_queue->social_media_type == SocialAccount::SOCIAL_MEDIA_TWITTER){
                        $api_result = $this->twShare($multi_post_sns_queue);
                        $api_result_json = $api_result;
                        $api_result = json_decode($api_result);
                        if ($api_result->id) $is_api_success = true;
                    }

                    // シェア後処理
                    if ($is_api_success === true) {
                        // 事後処理
                        call_user_func(array(self, $this->getCallbackFuncName($multi_post_sns_queue->callback_function_type)), $multi_post_sns_queue, MultiPostSnsQueue::EXECUTE_STATUS_SUCCESS);

                        // 最終処理
                        $this->terminate($multi_post_sns_queue, MultiPostSnsQueue::EXECUTE_STATUS_SUCCESS, $api_result_json);
                    }else{
                        // 事後処理
                        call_user_func(array(self, $this->getCallbackFuncName($multi_post_sns_queue->callback_function_type)), $multi_post_sns_queue, MultiPostSnsQueue::EXECUTE_STATUS_ERROR);
                        $this->logger->error('MultiPostSnsShare#MultiPostSnsShare api result failed. multi_post_sns_queue_id:' . $multi_post_sns_queue->id . ', api_result:' . $api_result_json);

                        // 最終処理
                        $this->terminate($multi_post_sns_queue, MultiPostSnsQueue::EXECUTE_STATUS_ERROR, $api_result_json);
                    }
                }

                $multi_post_sns_queue_transaction->commit();

            } catch (Exception $e) {
                $this->logger->error('MultiPostSnsShare#executeProcess occurred exception. multi_post_sns_queue_id=' . $multi_post_sns_queue->id . ', exception:' . $e);

                // 事後処理
                call_user_func(array(self, $this->getCallbackFuncName($multi_post_sns_queue->callback_function_type)), $multi_post_sns_queue, MultiPostSnsQueue::EXECUTE_STATUS_ERROR);

                // 最終処理
                $this->terminate($multi_post_sns_queue, MultiPostSnsQueue::EXECUTE_STATUS_ERROR, json_encode($e));

                $multi_post_sns_queue_transaction->commit();
            }
        }
    }

    /**
     * シェア後に実行するFunctionを取得する
     * @param $type
     * @return mixed
     */
    private function getCallbackFuncName($type) {
        return MultiPostSnsQueue::$callback_function[$type];
    }

    /**
     * FBシェア
     * @param $multi_post_sns_queue
     * @return bool
     */
    private function fbShare($multi_post_sns_queue) {
        $this->facebook_api_client->setToken($multi_post_sns_queue->access_token);

        $link_options = array();
        if (!Util::isNullOrEmpty($multi_post_sns_queue->share_image_url)) {
            $link_options['picture'] = $multi_post_sns_queue->share_image_url;
        }

        return $this->facebook_api_client->postShare($multi_post_sns_queue->shared_text, $multi_post_sns_queue->share_url, $link_options);
    }

    /**
     * TWシェア
     * @param $multi_post_sns_queue
     * @return bool
     */
    private function twShare($multi_post_sns_queue) {
        return $this->postTweet($multi_post_sns_queue);
    }

    /**
     * データ付きツイート
     * @param $multi_post_sns_queue
     * @return API|bool
     */
    private function postTweet($multi_post_sns_queue) {
        $twitter = new Twitter(
            config('@twitter.User.ConsumerKey'),
            config('@twitter.User.ConsumerSecret'),
            $multi_post_sns_queue->access_token,
            $multi_post_sns_queue->access_refresh_token
        );

        if ($multi_post_sns_queue->callback_function_type == MultiPostSnsQueue::CALLBACK_UPDATE_PHOTO_USER_SHARE) {
            $photo_user = $this->photo_user_service->getPhotoUserById($multi_post_sns_queue->callback_parameter);
            $cp_act = CpInfoContainer::getInstance()->getCpActionById($photo_user->cp_action_id);
            $cp = CpInfoContainer::getInstance()->getCpById($cp_act->getCpActionGroup()->cp_id);
            if ($multi_post_sns_queue->shared_text) {
                $tweet = $multi_post_sns_queue->shared_text . ' / ' . $cp->getTitle() . ' ' . $multi_post_sns_queue->share_url;
            }else{
                $tweet = $cp->getTitle() . ' ' . $multi_post_sns_queue->share_url;
            }
        } else if ($multi_post_sns_queue->callback_function_type == MultiPostSnsQueue::CALLBACK_UPDATE_COMMENT_USER_SHARE) {
            $tweet = $this->cutTweetText($multi_post_sns_queue->shared_text, $multi_post_sns_queue->share_url);
        } else {
            $tweet = $multi_post_sns_queue->shared_text;

            if (!Util::isNullOrEmpty($multi_post_sns_queue->share_url)) {
                $tweet .= ' ' . $multi_post_sns_queue->share_url;
            }
        }

        $middle_image_url = "";
        if ($this->isYoutubeImage($multi_post_sns_queue->share_image_url)) {
            $middle_image_url = $this->getMiddleYoutubeImage($multi_post_sns_queue->share_image_url);
        } else {
            if ($multi_post_sns_queue->callback_function_type == MultiPostSnsQueue::CALLBACK_UPDATE_PHOTO_USER_SHARE) {
                $middle_image_url = StorageClient::getMiddleImageUrl($multi_post_sns_queue->share_image_url);
            } else if ($multi_post_sns_queue->callback_function_type == MultiPostSnsQueue::CALLBACK_UPDATE_POPULAR_VOTE_USER_SHARE) {
                $middle_image_url = StorageClient::getRegularImageUrl($multi_post_sns_queue->share_image_url);
            }
        }

        if (Util::isNullOrEmpty($middle_image_url)) {
            return $twitter->postTweet($tweet);
        }

        $upload_media_json_data_result = $twitter->uploadMedia($middle_image_url);
        $upload_media_data_result = json_decode($upload_media_json_data_result);
        return $twitter->postTweetWithMedia($tweet, array($upload_media_data_result->media_id_string));
    }

    /**
     * @param $tweet_text
     * @param $tweet_url
     * @return mixed|string
     */
    private function cutTweetText($tweet_text, $tweet_url) {
        $adding_text = '…';
        $tweet_text = str_replace("\r\n", "\n", $tweet_text);
        $tweet_max_length = CpTweetAction::MAX_TEXT_LENGTH;

        if (!Util::isNullOrEmpty($tweet_url)) {
            $tweet_max_length -= (CpTweetAction::URL_TEXT_LENGTH + mb_strlen(' '));
        }

        $shared_text = $tweet_text;
        $tweet_text_length = mb_strlen($tweet_text, 'UTF-8');

        if ($tweet_text_length > $tweet_max_length) {
            $tweet_max_length -= mb_strlen($adding_text);
            $shared_text = mb_substr($tweet_text, 0, $tweet_max_length, 'UTF-8') . $adding_text;
        }

        if (!Util::isNullOrEmpty($tweet_url)) {
            $shared_text .= ' ' . $tweet_url;
        }

        return $shared_text;
    }

  /**
     * フォト投稿後シェア後処理
     * @param $multi_post_sns_queue
     * @param $execute_status
     */
    private function updatePhotoUserShare($multi_post_sns_queue, $execute_status) {
        $photo_user_share = $this->photo_user_share_service->getPhotoUserSharesByPhotoUserIdAndSnsType($multi_post_sns_queue->callback_parameter, $multi_post_sns_queue->social_media_type);
        $photo_user_share->execute_status = $execute_status;
        $this->photo_user_share_service->update($photo_user_share);
    }

    /**
     * 人気投票シェア後処理
     * @param $multi_post_sns_queue
     * @param $execute_status
     */
    private function updatePopularVoteUserShare($multi_post_sns_queue, $execute_status) {
        $popular_vote_user_share = $this->popular_vote_user_share_service->getPopularVoteUserShareByPopularVoteUserIdAndSocialMediaType($multi_post_sns_queue->callback_parameter, $multi_post_sns_queue->social_media_type);
        $popular_vote_user_share->execute_status = $execute_status;
        $this->popular_vote_user_share_service->updatePopularVoteUserShare($popular_vote_user_share);
    }

    /**
     * コメント投稿シェア後処理
     * @param $multi_post_sns_queue
     * @param $execute_status
     */
    private function updateCommentUserShare($multi_post_sns_queue, $execute_status) {
        $comment_user_share = $this->comment_user_service->getCommentUserShareByCommentUserRelationIdAndSocialMediaId($multi_post_sns_queue->callback_parameter, $multi_post_sns_queue->social_media_type);
        $comment_user_share->execute_status = $execute_status;
        $this->comment_user_service->updateCommentUserShare($comment_user_share);
    }

    /**
     * 最終処理
     * @param $multi_post_sns_queue
     * @param $execute_status
     * @param $api_result_json
     */
    private function terminate($multi_post_sns_queue, $execute_status, $api_result_json) {
        $multi_post_sns_queue->api_result = $api_result_json;
        if ($execute_status == MultiPostSnsQueue::EXECUTE_STATUS_ERROR) {
            $multi_post_sns_queue->error_flg = 1;
        }else if($execute_status == MultiPostSnsQueue::EXECUTE_STATUS_SUCCESS) {
            $multi_post_sns_queue->del_flg = 1;
        }
        $this->multi_post_sns_queue_service->update($multi_post_sns_queue);
    }

    /**
     * YoutubeImageUrlか判別するメソッド
     * @param $share_image_url
     * @return bool|int
     */
    private function isYoutubeImage($share_image_url) {
        $patterns = array('#^http://img.youtube.com/#', '#^https//i.ytimg.com/#');
        $matched = false;

        foreach ($patterns as $pattern) {
            $matched |= preg_match($pattern, $share_image_url);
        }

        return $matched;
    }

    /**
     * YoutubeImage (middle) を取得するメソッド
     * @param $share_image_url
     * @return string
     */
    private function getMiddleYoutubeImage($share_image_url) {
        return preg_replace('!/[^/]*$!', '/', $share_image_url) . '0.jpg';
    }
}
