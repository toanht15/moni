<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
class CreateCpActionFooter extends aafwWidgetBase {
	public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $cp_action = $cp_flow_service->getCpActionById($params['action_id']);

        $cp_next_actions = $cp_action->getCpNextActions();
        $cp_prev_actions = $cp_action->getCpPrevActions();

        if ($cp_next_actions) {

            $next_action = $cp_next_actions->current();
            $next_action = $cp_flow_service->getCpActionById($next_action->cp_next_action_id);
            $next_group = $cp_flow_service->getCpActionGroupById($next_action->cp_action_group_id);

            foreach ($cp_next_actions as $cp_next_action) {

                $cp_next_action = $cp_flow_service->getCpActionById($cp_next_action->cp_next_action_id);
                $cp_next_group = $cp_flow_service->getCpActionGroupById($cp_next_action->cp_action_group_id);

                if ($cp_next_group->order_no < $next_group->order_no) {
                    $next_action = $cp_next_action;
                    $next_group = $cp_next_group;
                }
            }
            $params['next_action'] = $next_action;
        }

        if ($cp_prev_actions) {

            $prev_action = $cp_prev_actions->current();
            $prev_action = $cp_flow_service->getCpActionById($prev_action->cp_action_id);
            $prev_group = $cp_flow_service->getCpActionGroupById($prev_action->cp_action_group_id);

            foreach ($cp_prev_actions as $cp_prev_action) {
                $cp_prev_action = $cp_flow_service->getCpActionById($cp_prev_action->cp_action_id);
                $cp_prev_group = $cp_flow_service->getCpActionGroupById($cp_prev_action->cp_action_group_id);

                if ($cp_prev_group->order_no > $prev_group->order_no) {
                    $prev_group = $cp_prev_group;
                    $prev_action = $cp_prev_action;
                }
            }
            $params['prev_action'] = $prev_action;

        }

		return $params;
	}
}