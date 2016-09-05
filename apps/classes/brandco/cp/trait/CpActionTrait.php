<?php

AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.vendor.Michelf.Markdown');
AAFW::import('jp.aainc.classes.CpInfoContainer');

trait CpActionTrait {

    protected $cp_actions;

    /**
     * @param $cp_action_group_id
     * @return mixed
     */
    public function getCpActionsByCpActionGroupId($cp_action_group_id) {

        $filter = array(
            'conditions' => array(
                "cp_action_group_id" => $cp_action_group_id,
            ),
            'order' => array(
                'name' => "order_no"
            )
        );
        return $this->cp_actions->find($filter);
    }

    /**
     * @param $cp_action_group_id
     * @param $action_type
     * @return mixed
     */
    public function getFixedCpActionsByCpActionGroupIdAndType($cp_action_group_id, $action_type) {

        $filter = array(
            'conditions' => array(
                "cp_action_group_id" => $cp_action_group_id,
                "type" => $action_type,
                "status" => CpAction::STATUS_FIX
            ),
            'order' => array(
                'name' => "order_no"
            )
        );
        return $this->cp_actions->find($filter);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getCpActionById($id) {
        return $this->cp_actions->findOne($id);
    }

    /**
     * @param $cp_action_group_id
     * @param $type
     * @return mixed
     */
    public function getCpActionByCpActionGroupIdAndType($cp_action_group_id, $type) {

        $filter = array(
            'conditions' => array(
                "cp_action_group_id" => $cp_action_group_id,
                "type" => $type
            ),
            'order' => array(
                'name' => "order_no"
            )
        );

        return $this->cp_actions->findOne($filter);
    }

    /**
     * @param $cp_action_group_id
     * @param $type
     * @return mixed
     */
    public function getCpActionsByCpActionGroupIdAndType($cp_action_group_id, $type) {

        $filter = array(
            'conditions' => array(
                "cp_action_group_id" => $cp_action_group_id,
                "type" => $type
            ),
            'order' => array(
                'name' => "order_no"
            )
        );

        return $this->cp_actions->find($filter);
    }

    /**
     * @param $group_id
     * @param $order_no
     * @return mixed
     */
    public function getCpActionByGroupIdAndOrderNo($group_id, $order_no) {
        $filter = array(
            'cp_action_group_id' => $group_id,
            'order_no' => $order_no
        );
        return $this->cp_actions->findOne($filter);
    }

    /**
     * @param $cp_action
     * @return mixed
     */
    public function getAfterActions($cp_action) {
        $filter = array(
            'conditions' => array(
                'cp_action_group_id' => $cp_action->cp_action_group_id,
                'order_no:>' => $cp_action->order_no
            ),
            'order' => array(
                'name' => 'order_no'
            )
        );
        return $this->cp_actions->find($filter);
    }

    /**
     * @param $cp_action_group_id
     * @param $type
     * @param $status
     * @param $order_no
     * @return mixed
     */
    public function createCpAction($cp_action_group_id, $type, $status, $order_no) {
        $cp_action = $this->cp_actions->createEmptyObject();
        $cp_action->cp_action_group_id = $cp_action_group_id;
        $cp_action->type = $type;
        $cp_action->status = $status;
        $cp_action->order_no = $order_no;
        return $this->cp_actions->save($cp_action);
    }

    public function updateCpAction(CpAction $cp_action) {
        $this->cp_actions->save($cp_action);
        CpInfoContainer::getInstance()->clearCpActionById($cp_action->id);
    }

    public function deleteCpAction(CpAction $cp_action) {
        $this->cp_actions->delete($cp_action);
        CpInfoContainer::getInstance()->clearCpActionById($cp_action->id);
    }

    /**
     * @param $cp_action_group_id
     * @return bool
     */
    public function isFixedCpActions($cp_action_group_id) {
        $cp_actions = $this->getCpActionsByCpActionGroupId($cp_action_group_id);
        foreach ($cp_actions as $cp_action) {
            if ($cp_action->status == CpAction::STATUS_DRAFT) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param CpAction $old_cp_action
     * @param $group_id
     * @return mixed
     */
    public function copyCpAction(CpAction $old_cp_action, $group_id) {
        $new_cp_action = $this->cp_actions->createEmptyObject();
        $new_cp_action->cp_action_group_id = $group_id;
        $new_cp_action->order_no = $old_cp_action->order_no;
        $new_cp_action->type = $old_cp_action->type;
        $new_cp_action->prefill_flg = $old_cp_action->prefill_flg;
        return $this->cp_actions->save($new_cp_action);
    }

    public function copyCpActionAndConfirm(CpAction $old_cp_action, $group_id){
        $new_cp_action = $this->cp_actions->createEmptyObject();
        $new_cp_action->cp_action_group_id = $group_id;
        $new_cp_action->order_no = $old_cp_action->order_no;
        $new_cp_action->type = $old_cp_action->type;
        $new_cp_action->prefill_flg = $old_cp_action->prefill_flg;
        $new_cp_action->status = CpAction::STATUS_FIX;
        return $this->cp_actions->save($new_cp_action);
    }

    public function getMaxStepNo ($cp_action_group_id) {
        $filter = array(
            'conditions' => array(
                'cp_action_group_id' => $cp_action_group_id,
            ),
            'order' => array(
                'name' => 'order_no',
                'direction' => 'desc'
            )
        );
        return $this->cp_actions->findOne($filter);
    }

    public function getMinStepNo ($cp_action_group_id) {
        $filter = array(
            'conditions' => array(
                'cp_action_group_id' => $cp_action_group_id,
            ),
            'order' => array(
                'name' => 'order_no',
                'direction' => 'asc'
            )
        );
        return $this->cp_actions->findOne($filter);
    }

    public function getCpActionGroupByAction($action_id) {
        $cp_action = $this->getCpActionById($action_id);
        if(!$cp_action) {
            return null;
        }
        $cp_action_group = $this->cp_action_groups->findOne($cp_action->cp_action_group_id);

        if(!$cp_action_group) {
            return null;
        }
        return $cp_action_group;
    }

    public function getDeliveryHistoryCacheByCpActionId($cp_action_id) {
        if (!$this->cache_manager) {
            $this->cache_manager = new CacheManager();
        }
        $cache = $this->cache_manager->getMessageHistoryCache($cp_action_id);

        return $cache ? $cache : array();
    }

    public function setDeliveryHistoryCacheByCpActionId($cp_action_id) {
        if (!$this->cache_manager) {
            $this->cache_manager = new CacheManager();
        }
        if (!$this->service_factory) {
            $this->service_factory = new aafwServiceFactory();
        }

        /** @var CpMessageDeliveryService $cp_message_delivery_service */
        $cp_message_delivery_service = $this->service_factory->create('CpMessageDeliveryService');
        $delivered_rsv = $cp_message_delivery_service->getDeliveredCpMessageDeliveryReservationByCpActionId($cp_action_id);
        $target = 0;
        $cache = array();
        if (!$delivered_rsv) {
            return array();
        }
        foreach ($delivered_rsv as $rsv) {
            if($rsv->delivery_type == CpMessageDeliveryReservation::DELIVERY_TYPE_NONE) {
                continue;
            }
            $count = $cp_message_delivery_service->getTargetsCountByReservationId($rsv->id);
            $cache[] = array('date' => $rsv->updated_at, 'count' => $count);
            $target += $count;
        }
        $this->cache_manager->setMessageHistoryCache($cp_action_id, json_encode($cache));
        if (!$target) {
            return array();
        }
        return $cache;
    }

    public function isCpActionFixed($cp_action) {
        $cp_action_groups = $cp_action->getCpActionGroups(array('del_flg' => 0));
        $cp_action_group = $cp_action_groups->toArray()[0];

        $is_fixed = ($cp_action->status == CpAction::STATUS_FIX);
        if ($cp_action_group->order_no == 1) {
            return true && $is_fixed;
        } else {

            $actions = $this->getCpActionsByCpActionGroupId($cp_action_group->id);
            $first_action = $actions->toArray()[0];

            /** @var CpMessageDeliveryService $cp_message_delivery_service */
            $cp_message_delivery_service = $this->getService('CpMessageDeliveryService');
            $delivered_rsv = $cp_message_delivery_service->getDeliveredCpMessageDeliveryReservationByCpActionId($first_action->id);
            if ($delivered_rsv) {
                return true && $is_fixed;
            }
        }

        return false;
    }
}
