<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpUserInstagramHashtag extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var CpUserListService $cp_user_list_service */
        $cp_user_list_service = $this->getService('CpUserListService');

        $cp_user_ids = array();
        foreach($params['fan_list_users'] as $fan_list_user) {
            if($fan_list_user->cp_user_id){
                $cp_user_ids[] = $fan_list_user->cp_user_id;
            }
        }

        $params['user_hashtag'] = $cp_user_list_service->getFanListInstagramHashtag($params['display_action_id'], $cp_user_ids);

        /** @var CpInstagramHashtagActionService $cp_instagram_hashtag_action_service */
        $cp_instagram_hashtag_action_service = $this->getService('CpInstagramHashtagActionService');
        $params['cp_instagram_hashtag_action'] =  $cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($params['display_action_id']);

        $params['colspan'] = 7;
        if($params['cp_instagram_hashtag_action']->approval_flg) {
            $params['colspan'] += 1;
        }

        $service_factory = new aafwServiceFactory();
        /** @var $brand_user_relation_service BrandsUsersRelationService */
        $params['brand_user_relation_service'] = $service_factory->create('BrandsUsersRelationService');

        return $params;
    }
}