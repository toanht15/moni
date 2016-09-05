<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpUserTwitterFollow extends aafwWidgetBase {

    public function doService($params = array()) {
        $cp_tw_follow_action_service = $this->getService('CpTwitterFollowActionService');

        $params['cp_twitter_follow_action'] = $cp_tw_follow_action_service->getCpTwitterFollowAction($params['display_action_id']);

        $cp_tw_follow_log_service = $this->getService('CpTwitterFollowLogService');
        $cp_user_ids = array();
        foreach ($params['fan_list_users'] as $element) {
            if ($element->cp_user_id) {
                $cp_user_ids[] = $element->cp_user_id;
            }
        }
        $params['cp_twitter_follow_log_list'] = $cp_tw_follow_log_service->getCpTwFollowLogsByCpUserListAndCpActionId($cp_user_ids, $params['cp_twitter_follow_action']->id);

        $service_factory = new aafwServiceFactory();
        /** @var $brand_user_relation_service BrandsUsersRelationService */
        $params['brand_user_relation_service'] = $service_factory->create('BrandsUsersRelationService');

        return $params;
    }
}