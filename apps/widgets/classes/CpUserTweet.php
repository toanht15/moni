<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpUserTweet extends aafwWidgetBase {

    public function doService($params = array()) {
        $cp_tweet_action_service = $this->getService('CpTweetActionService');

        $params['cp_tweet_action'] = $cp_tweet_action_service->getCpTweetAction($params['display_action_id']);

        $tweet_message_service = $this->getService('TweetMessageService');
        $cp_user_ids = array();
        foreach ($params['fan_list_users'] as $element) {
            if ($element->cp_user_id) {
                $cp_user_ids[] = $element->cp_user_id;
            }
        }
        $params['tweet_content_list'] = $tweet_message_service->getTweetContentByCpUserListAndConcreteActionId($cp_user_ids, $params['cp_tweet_action']->id, $params['cp_tweet_action']->tweet_fixed_text);

        $service_factory = new aafwServiceFactory();
        /** @var $brand_user_relation_service BrandsUsersRelationService */
        $params['brand_user_relation_service'] = $service_factory->create('BrandsUsersRelationService');

        return $params;
    }
}