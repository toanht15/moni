<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpUserFacebookLike extends aafwWidgetBase {

    public function doService($params = array()) {
        $cp_fb_like_action_service = $this->getService('CpFacebookLikeActionService');
        $params['cp_facebook_like_action'] = $cp_fb_like_action_service->getCpFacebookLikeAction($params['display_action_id']);

        $cp_user_ids = array();
        foreach ($params['fan_list_users'] as $fan_list_user) {
            $cp_user_ids[] = $fan_list_user->cp_user_id;
        }

        $cp_fb_like_log_service = $this->getService('CpFacebookLikeLogService');
        $params['cp_facebook_like_statuses'] = $cp_fb_like_log_service->getCpFbLikeLogStatuses($cp_user_ids, $params['cp_facebook_like_action']->cp_action_id);

        $service_factory = new aafwServiceFactory();
        /** @var $brand_user_relation_service BrandsUsersRelationService */
        $params['brand_user_relation_service'] = $service_factory->create('BrandsUsersRelationService');

        return $params;
    }
}