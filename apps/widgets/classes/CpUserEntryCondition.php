<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpUserEntryCondition extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $params['cp_action_groups'] = $cp_flow_service->getCpActionGroupsByCpId($params['cp_id']);
        /** @var CpUserListService $cp_user_list_service */
        $cp_user_list_service = $this->getService('CpUserListService');
        foreach($params['cp_action_groups'] as $group) {
            $cp_actions = $cp_flow_service->getCpActionsByCpActionGroupId($group->id);
            if(!$cp_actions) {
                continue;
            }
            foreach($cp_actions as $cp_action) {
                $cp_user_ids = array();
                foreach($params['fan_list_users'] as $fan_list_user) {
                    if($fan_list_user->cp_user_id) {
                        $cp_user_ids[] = $fan_list_user->cp_user_id;
                    }
                }
                $params['fan_list_statuses'][$cp_action->id] = $cp_user_list_service->getFanListStatus($cp_user_ids, $cp_action);
            }
            $cp_actions = $cp_actions->toArray();
            $params['cp_actions'][$group->id] = $cp_actions;
            $params['action_col'][$group->id] = count($cp_actions);
            if ($params['show_sent_time']) {
                /** @var CpMessageDeliveryService $message_delivery_service */
                $message_delivery_service = $this->getService('CpMessageDeliveryService');
                $params['target_count'][$group->id] = $message_delivery_service->getDeliveredTargetCountByActionId($cp_actions[0]->id);
                $params['fan_list_send_time_array'][$group->id] = $cp_user_list_service->getFanListSendTime($params['fan_list_users'], $cp_actions[0]->id);
            }
        }

        $service_factory = new aafwServiceFactory();
        /** @var $brand_user_relation_service BrandsUsersRelationService */
        $params['brand_user_relation_service'] = $service_factory->create('BrandsUsersRelationService');

        return $params;
    }

}