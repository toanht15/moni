<?php

AAFW::import('jp.aainc.classes.entities.CpActionGroup');

trait CpActionGroupTrait {

    protected $cp_action_groups;

    /**
     * @param $cp_id
     * @return mixed
     */
    public function getCpActionGroupsByCpId($cp_id) {

        $filter = array(
            'conditions' => array(
                "cp_id" => $cp_id,
            ),
            'order' => array(
                'name' => "order_no"
            )
        );
        return $this->cp_action_groups->find($filter);
    }

    /**
     * @param $cp_id
     * @param $order_no
     * @return mixed
     */
    public function getCpActionGroupByCpIdAndOrderNo($cp_id, $order_no) {
        $filter = array(
            "cp_id" => $cp_id,
            "order_no" => $order_no
        );
        return $this->cp_action_groups->findOne($filter);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getCpActionGroupById($id) {
        return $this->cp_action_groups->findOne($id);
    }

    /**
     * @param $cp_id
     * @param $action_group_id
     * @return mixed
     */
    public function getAfterActionGroups($cp_id, $action_group_id) {
        $action_group = $this->getCpActionGroupById($action_group_id);
        $filter = array(
            'conditions' => array(
                'cp_id' => $cp_id,
                'order_no:>' => $action_group->order_no
            ),
            'order' => array(
                'name' => 'order_no'
            )
        );
        return $this->cp_action_groups->find($filter);
    }

    /**
     * @param $cp_id
     * @param $order_no
     * @return mixed
     */
    public function createCpActionGroup($cp_id, $order_no) {
        $cp_action_group = $this->cp_action_groups->createEmptyObject();
        $cp_action_group->cp_id = $cp_id;
        $cp_action_group->order_no = $order_no;
        return $this->cp_action_groups->save($cp_action_group);

    }

    public function updateCpActionGroup(CpActionGroup $cp_action_group) {
        $this->cp_action_groups->save($cp_action_group);
    }

    public function deleteCpActionGroup(CpActionGroup $cp_action_group) {
        $this->cp_action_groups->delete($cp_action_group);
    }

    /**
     * @param CpActionGroup $action_group
     * @param $cp_id
     * @return mixed
     */
    public function copyCpActionGroup (CpActionGroup $action_group, $cp_id) {
        $cp_action_group = $this->cp_action_groups->createEmptyObject();
        $cp_action_group->cp_id = $cp_id;
        $cp_action_group->order_no = $action_group->order_no;
        return $this->cp_action_groups->save($cp_action_group);
    }

    /**
     * @param $group_id
     * @return int
     */
    public function getMinOrderOfActionInGroup ($group_id) {
        $order = 0;
        $group = $this->getCpActionGroupById($group_id);
        $filter = array(
            'cp_id' => $group->cp_id,
            'order_no:<' => $group->order_no
        );

        $before_groups = $this->cp_action_groups->find($filter);

        foreach ($before_groups as $before_group) {
            $filter = array(
                'conditions' => array(
                    'cp_action_group_id' => $before_group->id,
                )
            );
            $order += $this->cp_actions->count($filter);
        }
        return $order;
    }
}

