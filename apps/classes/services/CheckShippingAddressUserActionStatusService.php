<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CheckShippingAddressUserActionStatusService extends aafwServiceBase {

    private $dataBuilder;
    private $cpFlowService;

    public function __construct() {
        $this->dataBuilder = aafwDataBuilder::newBuilder();
        $this->cpFlowService = $this->getService('CpFlowService');
    }

    public function getShippingAddressActionIdsInGroupBefore($cpId, $currentActionGroup) {
        $actionIds = array();
        for ($groupOrder = 1; $groupOrder < $currentActionGroup->order_no; $groupOrder++) {
            $targetGroup = $this->cpFlowService->getCpActionGroupByCpIdAndOrderNo($cpId,$groupOrder);
            $actions = $this->cpFlowService->getCpActionsByCpActionGroupId($targetGroup->id);
            foreach ($actions as $action) {
                if ($action->type == CpAction::TYPE_SHIPPING_ADDRESS) {
                    $actionIds[] = $action->id;
                }
            }
        }
        return $actionIds;
    }

    public function getNotHaveShippingAddressUserCountFromTargetFan($cpId, $targetUsers, $shippingAddressActionId) {
        $shippingAddressAction = $this->cpFlowService->getCpActionById($shippingAddressActionId);
        $firstActionId = $this->cpFlowService->getFirstActionInGroupByAction($shippingAddressAction)->id;

        $sql = "
            SELECT
                count(u.id) as num
            FROM
                users u
            WHERE
                NOT EXISTS(
                    SELECT
                         ca.id
                    FROM
                        cp_user_action_statuses ca
                    INNER JOIN
                        cp_users cu
                        ON ca.cp_user_id = cu.id AND
                        ca.status = 1 AND
                        ca.del_flg = 0 AND
                        ca.cp_action_id = ".$shippingAddressActionId."
                    WHERE
                        cu.user_id = u.id AND
                        cu.cp_id = " . $cpId . " AND
                        cu.del_flg = 0
                )
            AND
                EXISTS(
                    SELECT
                         cm.id
                    FROM
                        cp_user_action_messages cm
                    INNER JOIN
                        cp_users cu
                        ON cm.cp_user_id = cu.id AND
                        cm.del_flg = 0 AND
                        cm.cp_action_id = ".$firstActionId."
                    WHERE
                        cu.user_id = u.id AND
                        cu.cp_id = " . $cpId . " AND
                        cu.del_flg = 0
                )
            AND
               u.id in (" . $targetUsers . ")
        ";
        $data = $this->dataBuilder->getBySQL($sql, []);
        return $data[0]['num'];
    }

    public function getNotHaveShippingAddressUserCountFromAllFan($searchQuery,$shippingAddressActionId) {
        $shippingAddressAction = $this->cpFlowService->getCpActionById($shippingAddressActionId);
        $firstActionId = $this->cpFlowService->getFirstActionInGroupByAction($shippingAddressAction)->id;

        $sql = "
            SELECT
                count(u.user_id) as num
            FROM (" . $searchQuery . ") u
            WHERE
                NOT EXISTS(
                    SELECT
                         ca.id
                    FROM
                        cp_user_action_statuses ca
                    WHERE
                        ca.cp_action_id = ".$shippingAddressActionId." AND
                        ca.cp_user_id = u.cp_user_id AND
                        ca.status = 1 AND
                        ca.del_flg = 0
                )
            AND
                EXISTS(
                    SELECT
                         cm.id
                    FROM
                        cp_user_action_messages cm
                    WHERE
                        cm.cp_action_id = ".$firstActionId." AND
                        cm.cp_user_id = u.cp_user_id AND
                        cm.del_flg = 0
                )";
        $data = $this->dataBuilder->getBySQL($sql, []);
        return $data[0]['num'];
    }

    public function isNotJoinShippingAddressUserActionFromTargetFan($cpId,$targetUsers, $shippingAddressActionIds) {
        foreach ($shippingAddressActionIds as $shippingAddressActionId) {
            if ($this->getNotHaveShippingAddressUserCountFromTargetFan($cpId,$targetUsers,$shippingAddressActionId) > 0) {
                return true;
            }
        }
        return false;
    }

    public function isNotJoinShippingAddressUserActionFromAllFan($searchQuery, $shippingAddressActionIds) {
        foreach ($shippingAddressActionIds as $shippingAddressActionId) {
            if ($this->getNotHaveShippingAddressUserCountFromAllFan($searchQuery,$shippingAddressActionId) > 0) {
                return true;
            }
        }
        return false;
    }

    public function getSearchQuery($pageInfo, $searchCondition) {
        /** @var CpCreateSqlService $create_sql_service*/
        $createSqlService = $this->getService('CpCreateSqlService');
        return $createSqlService->getUserSql($pageInfo, $searchCondition, null, null, null);
    }
}
