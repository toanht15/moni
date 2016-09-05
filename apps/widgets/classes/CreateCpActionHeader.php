<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
class CreateCpActionHeader extends aafwWidgetBase {
	public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $cp = $cp_flow_service->getCpById($params['cp_id']);

        $params['groups'] = $cp_flow_service->getCpActionGroupsByCpId($cp->id);
        $params['first_action'] = $cp_flow_service->getFirstActionOfCp($cp->id);
        //check all action is completed
        $isReady = true;
        $group = $params['groups']->current();
        $actions = $cp_flow_service->getCpActionsByCpActionGroupId($group->id);

        foreach($actions as $action){
            if($isReady && $action->status != CpAction::STATUS_FIX){
                $isReady = false;
            }
        }

        //基本設定
        if($cp->fix_basic_flg != CpAction::STATUS_FIX) {
            $isReady = false;
        }

        //集客設定
        if($cp->join_limit_flg == cp::JOIN_LIMIT_OFF && $cp->fix_attract_flg != CpAction::STATUS_FIX) {
            $isReady = false;
        }

        $params['cp'] = $cp;
        $params['isReady'] = $isReady;
		return $params;
	}
}