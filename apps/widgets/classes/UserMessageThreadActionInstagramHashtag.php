<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class UserMessageThreadActionInstagramHashtag extends aafwWidgetBase {

    private $cp_instagram_hashtag_action_service;
    private $instagram_hashtag_user_service;

    const PERPAGE_SP = 6;
    const PERPAGE_PC = 8;

    public function doService($params = array()) {

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $this->cp_instagram_hashtag_action_service = $this->getService('CpInstagramHashtagActionService');
        $this->instagram_hashtag_user_service = $this->getService('InstagramHashtagUserService');

        $params['is_last_action'] = $cp_flow_service->isLastCpActionInGroup($params['message_info']['cp_action']->id);

        $params['cp_instagram_hashtag_action'] = $this->cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($params['message_info']['cp_action']->id);

        $params['instagram_hashtag_user'] = $this->instagram_hashtag_user_service->getInstagramHashtagUserByCpActionIdAndCpUserId($params['message_info']['cp_action']->id, $params['cp_user']->id);

        // みんなの投稿
        if (Util::isSmartPhone()) {
            $result = $this->instagram_hashtag_user_service->getRandomInstagramHashtagUserPostsByCpActionId($params['message_info']['cp_action']->id, InstagramHashtagUserPost::PERPAGE_CP_EVERYONE_POST_SP);
            if (count($result) >= self::PERPAGE_SP) {
                $params['instagram_hashtag_user_random_posts'] = $result;
            }
        }else{
            $result = $this->instagram_hashtag_user_service->getRandomInstagramHashtagUserPostsByCpActionId($params['message_info']['cp_action']->id, InstagramHashtagUserPost::PERPAGE_CP_EVERYONE_POST_PC);
            if (count($result) >= self::PERPAGE_PC) {
                $params['instagram_hashtag_user_random_posts'] = $result;
            }
        }

        return $params;
    }
}
