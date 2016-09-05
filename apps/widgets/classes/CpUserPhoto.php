<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpUserPhoto extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var PhotoUserService $photo_user_service */
        $photo_user_service = $this->getService('PhotoUserService');
        $cp_user_list_service = $this->getService('CpUserListService');
        $params['cp_photo_action'] = $photo_user_service->getCpPhotoActionByCpActionId($params['display_action_id']);
        if ($params['fan_list_users']) {
            $cp_user_ids = array();
            foreach($params['fan_list_users'] as $fan_list_user) {
                if($fan_list_user->cp_user_id){
                    $cp_user_ids[] = $fan_list_user->cp_user_id;
                }
            }
            if($params['cp_photo_action']->fb_share_required || $params['cp_photo_action']->tw_share_required){
                $params['user_photo_array'] = $cp_user_list_service->getPhotoFanListUser($params['display_action_id'], $cp_user_ids, true);
            } else {
                $params['user_photo_array'] = $cp_user_list_service->getPhotoFanListUser($params['display_action_id'], $cp_user_ids, false);
            }
        }
        $params['cp_photo_action'] = $photo_user_service->getCpPhotoActionByCpActionId($params['display_action_id']);

        $params['colspan'] = 1;
        if($params['cp_photo_action']->title_required) {
            $params['colspan'] += 1;
        }
        if($params['cp_photo_action']->comment_required) {
            $params['colspan'] += 1;
        }
        if($params['cp_photo_action']->fb_share_required || $params['cp_photo_action']->tw_share_required) {
            $params['colspan'] += 2;
        }
        if($params['cp_photo_action']->panel_hidden_flg) {
            $params['colspan'] += 1;
        }

        $service_factory = new aafwServiceFactory();
        /** @var $brand_user_relation_service BrandsUsersRelationService */
        $params['brand_user_relation_service'] = $service_factory->create('BrandsUsersRelationService');

        return $params;
    }

}