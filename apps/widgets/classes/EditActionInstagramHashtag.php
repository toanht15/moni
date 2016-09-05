<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpInstagramHashtagActionManager');

class EditActionInstagramHashtag extends aafwWidgetBase {

    public function doService($params = array()) {

        $cp_instagram_hashtag_action_manager = new CpInstagramHashtagActionManager();
        $cp_actions = $cp_instagram_hashtag_action_manager->getCpActions($params['action_id']);

        $params['action'] = $cp_actions[0];
        $params['cp_instagram_hashtag_action'] = $cp_actions[1];

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $params['cp'] = $cp_flow_service->getCpById($params['cp_id']);
        if ($params['cp']->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($params['cp']->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        return $params;
    }
}
