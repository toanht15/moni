<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpListService extends aafwServiceBase {

    public function __construct() {
        $this->cp_flow_service = $this->getService("CpFlowService");
    }

    public function getListPublicCp($cp_ids) {
        if(!$cp_ids) {
            return;
        }
        $cps = array();
        $db = new aafwDataBuilder();
        $sql = "SELECT G.cp_id cp_id,
                G.id group_id,
                G.order_no group_order_no,
                A.order_no action_order_no,
                A.id action_id,
                A.type type
                FROM cp_action_groups G
                INNER JOIN cp_actions A ON G.id = A.cp_action_group_id AND A.del_flg = 0
                WHERE G.cp_id IN (".implode(',',$cp_ids).") AND G.del_flg = 0
                ORDER BY cp_id DESC , group_order_no ASC , action_order_no ASC
        ";
        $group_actions = $db->getBySQL($sql, array());
        foreach($group_actions as $group_action) {
            $cps[$group_action['cp_id']][$group_action['group_id']]['group_order_no'] = $group_action['group_order_no'];
            $cps[$group_action['cp_id']][$group_action['group_id']][$group_action['action_id']]['action_order_no'] = $group_action['action_order_no'];
            $cps[$group_action['cp_id']][$group_action['group_id']][$group_action['action_id']]['type'] = $group_action['type'];
        }
        return $cps;
    }

    public function getStepNo($group) {
        $minStepNo = reset($group)['action_order_no'];
        $maxStepNo = end($group)['action_order_no'];
        return array($minStepNo, $maxStepNo);
    }


    public function getDeliveredLogs($group) {
        reset($group);
        if($group[key($group)]['type'] == CpAction::TYPE_ANNOUNCE_DELIVERY) {
            return array();
        }
        $delivered_rsv_logs = $this->cp_flow_service->getDeliveryHistoryCacheByCpActionId(key($group));
        if(!$delivered_rsv_logs) {
            $delivered_rsv_logs = $this->cp_flow_service->setDeliveryHistoryCacheByCpActionId(key($group));
        }
        return $delivered_rsv_logs;
    }

    public function getActionData($cp, $action_key, $action, $group_order_no) {
        $cp_actions = $this->setCpAction($action_key, $action);
        $cp_action_detail = $cp_actions->getCpActionDetail();
        $cp_action_data = $cp_actions->getCpActionData();

        if ($cp->type == Cp::TYPE_CAMPAIGN && $action['type'] == CpAction::TYPE_QUESTIONNAIRE && $action['action_order_no'] == 1 && $group_order_no == 1) {
            $cp_action_detail['icon'] = 'enqueteAndCp1.png';
        }

        return array($cp_action_detail, $cp_action_data);
    }

    private function setCpAction($action_key, $action) {
        $cp_actions = aafwEntityFactory::create('CpAction');
        $cp_actions->id = $action_key;
        $cp_actions->type = $action['type'];
        return $cp_actions;
    }
}