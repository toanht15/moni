<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.exception.api.APIValidationException');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class api_execute_popular_vote_action extends ExecuteActionBase {

    public $CsrfProtect = true;
    protected $ContainerName = 'api_execute_photo_action';

    private $logger;

    /** @var  PopularVoteUserService $popular_vote_user_service */
    private $popular_vote_user_service;
    /** @var  MultiPostSnsQueueService $multi_post_sns_queue_service */
    private $multi_post_sns_queue_service;
    /** @var  PopularVoteUserShareService $popular_vote_user_share_service */
    private $popular_vote_user_share_service;
    /** @var  CpUserService $cp_user_service */
    private $cp_user_service;

    public function doThisFirst() {
        $this->popular_vote_user_service = $this->getService('PopularVoteUserService');
        $this->popular_vote_user_share_service = $this->getService('PopularVoteUserShareService');
        $this->multi_post_sns_queue_service = $this->getService('MultiPostSnsQueueService');
        $this->cp_user_service = $this->getService('CpUserService');
    }

    public function saveData() {
        $popular_vote_user = $this->popular_vote_user_service->createEmptyPopularVoteUser();
        $popular_vote_user->cp_action_id    = $this->cp_action_id;
        $popular_vote_user->cp_user_id      = $this->cp_user_id;
        $popular_vote_user->cp_popular_vote_candidate_id = $this->cp_popular_vote_candidate_id;
        $popular_vote_user = $this->popular_vote_user_service->updatePopularVoteUser($popular_vote_user);

        if ($this->fb_share_flg) {
            if($this->isCanPostSNS()) {
                $this->createMultiPostSnsQueues($popular_vote_user, SocialAccount::SOCIAL_MEDIA_FACEBOOK);
            }

            $this->createPopularVoteUserShare($popular_vote_user->id, SocialAccount::SOCIAL_MEDIA_FACEBOOK);
        }

        if ($this->tw_share_flg) {
            if($this->isCanPostSNS()) {
                $this->createMultiPostSnsQueues($popular_vote_user, SocialAccount::SOCIAL_MEDIA_TWITTER);
            }

            $this->createPopularVoteUserShare($popular_vote_user->id, SocialAccount::SOCIAL_MEDIA_TWITTER);
        }
    }

    public function validate() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();

        try {
            // validate共通の処理
            $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
            $validator->validate();
            if (!$validator->isValid()) {
                throw new APIValidationException($validator->getErrors());
            }

            $validator_definition = array(
                'cp_popular_vote_candidate_id' => array(
                    'required' => true,
                ),
                'share_text' => array(
                    'type' => 'str',
                    'length' => 50
                ),
            );
            $validator = new aafwValidator($validator_definition);
            $validator->validate($this->POST);
            if (!$validator->isValid()) {
                $errors = array();
                foreach ($validator->getErrors() as $key => $value) {
                    $errors[$key] = $validator->getMessage($key);
                }

                throw new APIValidationException($errors);
            }

            // 同一アクションですでに参加済みかどうかを調べる
            if ($this->popular_vote_user_service->getPopularVoteUserByIds($this->cp_action_id, $this->cp_user_id)) {
                throw new APIValidationException();
            }
        } catch (APIValidationException $e) {
            $json_data = $this->createAjaxResponse('ng', array(), $e->getErrorMessage());
            $this->assign('json_data', $json_data);

            return false;
        }

        return true;
    }

    public function createPopularVoteUserShare($popular_vote_user_id, $social_media_type) {
        $popular_vote_user_share = $this->popular_vote_user_share_service->createEmptyPopularVoteUserShare();
        $popular_vote_user_share->popular_vote_user_id = $popular_vote_user_id;
        $popular_vote_user_share->social_media_type = $social_media_type;
        $popular_vote_user_share->share_text = $this->share_text;
        $this->popular_vote_user_share_service->updatePopularVoteUserShare($popular_vote_user_share);
    }

    public function createMultiPostSnsQueues($popular_vote_user, $social_media_type) {
        $app_id = $this->getBrand()->app_id;
        $token_array = $this->getAccessTokenInfo($this->Data['pageStatus']['userInfo'], $social_media_type, $app_id);

        if (count($token_array)) {
            $this->logger->info('createMultiPostSnsQueues#social_media_access_token:' . $token_array['social_media_access_token']);
            $this->logger->info('createMultiPostSnsQueues#social_media_access_refresh_token:' . $token_array['social_media_access_refresh_token']);
        } else {
            $this->logger->error('api_execute_popular_vote_action#getAccessTokenInfo error. popular_vote_user_id=' . $popular_vote_user->id);
        }

        $cp_popular_vote_candidate = $this->getService('CpPopularVoteActionService')->getCpPopularVoteCandidateById($popular_vote_user->cp_popular_vote_candidate_id);

        $multi_post_sns_queue = $this->multi_post_sns_queue_service->createEmptyObject();
        $multi_post_sns_queue->access_token = $token_array['social_media_access_token'];
        $multi_post_sns_queue->access_refresh_token = $token_array['social_media_access_refresh_token'];
        $multi_post_sns_queue->callback_parameter = $popular_vote_user->id;
        $multi_post_sns_queue->social_media_type = $social_media_type;
        $multi_post_sns_queue->share_text = $this->share_text;

        if ($social_media_type == SocialAccount::SOCIAL_MEDIA_TWITTER) {
            if ($popular_vote_user->cp_action_id == 32260) { // シェア文言ハードコーディング（SUBWAY-15）
                $multi_post_sns_queue->share_text .= (strlen($this->share_text) > 0) ? ' / ' : '';
                $multi_post_sns_queue->share_text .= "NEWサブウェイクラブに{$cp_popular_vote_candidate->title}をプラス！もれなくトッピング無料パスがもらえるキャンペーン中！ #サブウェイ倶楽部";
            } else {
                $multi_post_sns_queue->share_text .= (strlen($this->share_text) > 0) ? '/' : '';
                $multi_post_sns_queue->share_text .= '「' .  $cp_popular_vote_candidate->title . '」に投票しました！';
            }
        }
        $multi_post_sns_queue->share_image_url = $cp_popular_vote_candidate->thumbnail_url;
        if ($popular_vote_user->cp_action_id == 32260) { // シェアURLハードコーディング（SUBWAY-15）
            $multi_post_sns_queue->share_url = 'http://goo.gl/6dsCNq';
        } else {
            $multi_post_sns_queue->share_url = ($this->share_url_type == CpPopularVoteAction::SHARE_URL_TYPE_RANKING) ? $this->share_url . '/' . $cp_popular_vote_candidate->id : $this->share_url;
        }
        $multi_post_sns_queue->share_title = '「' . $cp_popular_vote_candidate->title . '」に投票しました！';
        $multi_post_sns_queue->share_description = '';

        $multi_post_sns_queue->callback_function_type = MultiPostSnsQueue::CALLBACK_UPDATE_POPULAR_VOTE_USER_SHARE;
        $multi_post_sns_queue->social_account_id = $this->getSNSAccountId($this->Data['pageStatus']['userInfo'], SocialAccount::$socialMediaTypeName[$social_media_type]);
        $this->multi_post_sns_queue_service->update($multi_post_sns_queue);
    }

    public function getAccessTokenInfo($user_info, $social_media_type, $app_id) {
        $user_sns_account_manager = new UserSnsAccountManager($user_info, null, $app_id);

        switch ($social_media_type) {
            case SocialAccount::SOCIAL_MEDIA_FACEBOOK:
                return $user_sns_account_manager->getSnsAccountInfo($social_media_type, SocialAccount::$socialMediaTypeName[SocialAccount::SOCIAL_MEDIA_FACEBOOK]);
                break;
            case SocialAccount::SOCIAL_MEDIA_TWITTER:
                return $user_sns_account_manager->getSnsAccountInfo($social_media_type, SocialAccount::$socialMediaTypeName[SocialAccount::SOCIAL_MEDIA_TWITTER]);
                break;
        }

        return [];
    }
}