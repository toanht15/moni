<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class MsgActionHeader extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var $serviceFactory aafwServiceFactory */
        $serviceFactory = new aafwServiceFactory();

        $cp_list_service = $serviceFactory->create('CpListService');

        $params['cp_status'] = $params['cp']->getStatus();
        $params['first_photo_action'] = null;
        $params['first_instagram_hashtag_action'] = null;

        if(!$params['group_array']) {
            $cp_ids[] = $params['cp']->id;
            $cps = $cp_list_service->getListPublicCp($cp_ids);
            $params['group_array'] = $cps[$params['cp']->id];
        }

        foreach ($params['group_array'] as $group) {
            foreach($group as $action_id => $action) {
                if (!is_array($action)) {
                    continue;
                }

                if($action['type'] == CpAction::TYPE_INSTAGRAM_HASHTAG) {
                    if($params['first_instagram_hashtag_action']) {
                        continue;
                    }
                    $params['first_instagram_hashtag_action'] = $action_id;
                }

                if($action['type'] == CpAction::TYPE_PHOTO) {
                    if($params['first_photo_action']) {
                        continue;
                    }
                    $params['first_photo_action'] = $action_id;
                }
            }
        }
        return $params;
    }
}

