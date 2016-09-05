<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class UserMessageThreadActionPopularVote extends aafwWidgetBase{

    public function doService( $params = array() ){
        /** @var CpPopularVoteActionService $cp_popular_vote_action_service */
        $cp_popular_vote_action_service = $this->getService('CpPopularVoteActionService');
        /** @var PopularVoteUserService $popular_vote_user_service */
        $popular_vote_user_service = $this->getService('PopularVoteUserService');
        /** @var PopularVoteUserShareService $popular_vote_user_share_service */
        $popular_vote_user_share_service = $this->getService('PopularVoteUserShareService');

        $params['popular_vote_user'] = $popular_vote_user_service->getPopularVoteUserByIds($params['message_info']['cp_action']->id, $params['cp_user']->id);

        $cp_popular_vote_candidates = $cp_popular_vote_action_service->getCpPopularVoteCandidateByCpPopularVoteActionId($params['message_info']['concrete_action']->id);
        foreach ($cp_popular_vote_candidates as $cp_popular_vote_candidate) {
            $params['candidate_list'][] = $cp_popular_vote_candidate;
        }

        $params = array_merge($params, $this->initSocialAccountsRequired($params['pageStatus'], $params['message_info']['concrete_action']->fb_share_required, $params['message_info']['concrete_action']->tw_share_required));

        if ($params['popular_vote_user']) {
            $popular_vote_user_share = $popular_vote_user_share_service->getPopularVoteUserShareByPopularVoteUserIdAndSocialMediaType($params['popular_vote_user']->id, SocialAccount::SOCIAL_MEDIA_FACEBOOK);
            if ($popular_vote_user_share) {
                $params['fb_shared'] = 1;
                $params['share_text'] = $popular_vote_user_share->share_text;
            }

            $popular_vote_user_share = $popular_vote_user_share_service->getPopularVoteUserShareByPopularVoteUserIdAndSocialMediaType($params['popular_vote_user']->id, SocialAccount::SOCIAL_MEDIA_TWITTER);
            if ($popular_vote_user_share) {
                $params['tw_shared'] = 1;
                $params['share_text'] = $popular_vote_user_share->share_text;
            }
        }

        if ($params['message_info']['concrete_action']->random_flg == CpPopularVoteAction::RANDOM_FLG) {
            shuffle($params['candidate_list']);
        }

        $params['selected_id'] = ($params['popular_vote_user']) ? $params['popular_vote_user']->cp_popular_vote_candidate_id : $params['candidate_list'][0]->id;
        $params['disable'] = ($params['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN) ? '' : 'disabled';

        $cp = CpInfoContainer::getInstance()->getCpById($params['cp_user']->cp_id);
        if ($params['message_info']['concrete_action']->share_url_type == CpPopularVoteAction::SHARE_URL_TYPE_CP) {

            $cp_og_info = $cp->getReferenceOpenGraphInfo();
            $params['share_url'] = $cp_og_info['url'];
        } else {
            $params['share_url'] = Util::rewriteUrl('popular_vote', 'ranking', array($params['message_info']['cp_action']->id));
        }

        $params['cp_url'] = Util::getCpURL($params['pageStatus']['brand']->id, $params['pageStatus']['brand']->directory_name, $cp->id);

        return $params;
    }

    /**
     * @param $pageStatus
     * @param $social_media_account_id
     * @return bool
     */
    private function checkPublishActions($pageStatus, $social_media_account_id){
        try {
            $user_sns_account_manager = new UserSnsAccountManager($pageStatus['userInfo'], null, $pageStatus['brand']->app_id);
            // $social_media_account_idは中で特に処理に使われてない
            $sns_account_info = $user_sns_account_manager->getSnsAccountInfo($social_media_account_id, 'Facebook');

            if (!$sns_account_info['social_media_access_token']) {
                return false;
            }

            $facebook_api_client = new FacebookApiClient(FacebookApiClient::BRANDCO_MODE_USER);
            $facebook_api_client->setToken($sns_account_info['social_media_access_token']);
            $permission_array = $facebook_api_client->getPermission();

            foreach ($permission_array as $permission) {
                if ($permission->permission === 'publish_actions' && $permission->status === 'granted') {
                    return true;
                }
            }
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('UserMessageThreadPopularVote#checkPublishActions error.');
            aafwLog4phpLogger::getDefaultLogger()->error($e);
        }

        return false;
    }

    /**
     * @param $pageStatus
     * @param bool|false $fb_share_required
     * @param bool|false $tw_share_required
     * @return array
     */
    public function initSocialAccountsRequired($pageStatus, $fb_share_required = false, $tw_share_required = false) {
        $params = array(
            'fb_connect'        => 0,
            'fb_has_permission' => 0,
            'tw_connect'        => 0,
        );

        if (count($pageStatus['userInfo']->socialAccounts)) {
            foreach ($pageStatus['userInfo']->socialAccounts as $social_account) {

                if ($fb_share_required && $social_account->socialMediaType === SocialAccount::$socialMediaTypeName[SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                    $params['fb_connect'] = 1;

                    if ($this->checkPublishActions($pageStatus, $social_account->socialMediaAccountID)) {
                        $params['fb_has_permission'] = 1;
                    } else {
                        // permissionがない場合は出さない
                        $params['fb_has_permission'] = 0;
                    }
                }

                if ($tw_share_required && $social_account->socialMediaType === SocialAccount::$socialMediaTypeName[SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                    $params['tw_connect'] = 1;
                }
            }
        }

        return $params;
    }
}