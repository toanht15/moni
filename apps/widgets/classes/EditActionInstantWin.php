<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditActionInstantWin extends aafwWidgetBase{

    public function doService( $params = array()) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $cp = $cp_flow_service->getCpById($params['cp_id']);
        if ($cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['cp'] = $cp;

        $cp_instant_win_action_manager = new CpInstantWinActionManager();
        list($cp_action, $cp_instant_win_action, $instant_win_prizes) = $cp_instant_win_action_manager->getCpActions($params['action_id']);

        $params['action'] = $cp_action;
        $instant_win_prizes = $instant_win_prizes->toArray();
        $params['instant_win_prizes'] = $instant_win_prizes;
        $params['start_date'] = $start_date->format('Y/m/d H:i');
        return $params;
    }
}