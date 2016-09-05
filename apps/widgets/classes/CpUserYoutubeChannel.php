<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpYoutubeChannelActionManager');

class CpUserYoutubeChannel extends aafwWidgetBase {

    public function doService($params = array()) {

        /** @var CpYoutubeChannelActionManager $cp_ytch_action_manager */
        $cp_ytch_action_manager = new CpYoutubeChannelActionManager();
        $cp_ytch_action = $cp_ytch_action_manager->getCpConcreteActionByCpActionId($params['display_action_id']);
        $params['item_title'] = Util::cutTextByWidth($cp_ytch_action->title, 750);

        $cp_user_ids = [];
        /** @var CpYoutubeChannelUserLogService $cp_yt_channel_user_log_service */
        $cp_yt_channel_user_log_service = $this->getService('CpYoutubeChannelUserLogService');
        foreach ($params['fan_list_users'] as $fan_list_user) {
            if (!$fan_list_user->cp_user_id) {
                continue;
            }
            $cp_user_ids[] = $fan_list_user->cp_user_id;
        }

        $params['user_logs'] = $cp_yt_channel_user_log_service->getLogsByCpUserIds($params['display_action_id'], $cp_user_ids);

        $service_factory = new aafwServiceFactory();
        /** @var $brand_user_relation_service BrandsUsersRelationService */
        $params['brand_user_relation_service'] = $service_factory->create('BrandsUsersRelationService');

        return $params;
    }
}