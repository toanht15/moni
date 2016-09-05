<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class UserMessageThreadActionEngagement extends aafwWidgetBase {

    public function doService($params) {

        $service_factory = new aafwServiceFactory();
        /** @var EngagementSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $service_factory->create('EngagementSocialAccountService');
        $params['engagement_social_account'] = $brand_social_account_service->getEngagementSocialAccount($params['message_info']['concrete_action']->id);

        return $params;
    }
}
