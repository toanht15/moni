<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase') ;

class CpUserShare extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var CpShareActionService $cp_share_action_service */
        $cp_share_action_service = $this->getService('CpShareActionService');
        $cp_share_action = $cp_share_action_service->getCpShareActionById($params['display_action_id']);
        if ($cp_share_action !== null && $params['fan_list_users']) {
            $cp_user_ids = array();
            foreach($params['fan_list_users'] as $fan_list_user) {
                $cp_user_ids[] = $fan_list_user->cp_user_id;
            }
            /** @var CpShareUserLogService $cp_share_user_log_service */
            $cp_share_user_log_service = $this->getService('CpShareUserLogService');
            $list_share_logs = $cp_share_user_log_service->getCpShareUserLogByCpShareActionIdAndFanListUser($cp_share_action->id, $cp_user_ids);
            $params['user_share_log_array'] = $cp_share_user_log_service->getListShareLogOfUser($list_share_logs);
        }

        $service_factory = new aafwServiceFactory();
        /** @var $brand_user_relation_service BrandsUsersRelationService */
        $params['brand_user_relation_service'] = $service_factory->create('BrandsUsersRelationService');

        return $params;
    }
}
