<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpHeaderActionList extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var $serviceFactory aafwServiceFactory */
        $serviceFactory = new aafwServiceFactory();
        $params['cp_list_service'] = $serviceFactory->create('CpListService');
        $params['cp_user_service'] = $serviceFactory->create('CpUserService');

        $params['exist_announce_actions'] = array();
        foreach($params['group_array'] as $group_id=>$groups) {
            foreach($groups as $action) {
                if($action['type'] == CpAction::TYPE_ANNOUNCE) {
                    $params['exist_announce_actions'][$group_id] = true;
                }
            }
        }
        return $params;
    }

}
