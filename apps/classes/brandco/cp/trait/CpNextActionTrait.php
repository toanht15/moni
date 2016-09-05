<?php


AAFW::import('jp.aainc.classes.entities.CpNextAction');

trait CpNextActionTrait {

    protected $cp_next_actions;

    /**
     * @param $id
     * @return mixed
     */
    public function getCpNextActionById($id) {
        return $this->cp_next_actions->findOne($id);
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpNextActionByCpActionId($cp_action_id) {

        $filter = array(
            'conditions' => array(
                "cp_action_id" => $cp_action_id,
            ),
        );
        return $this->cp_next_actions->findOne($filter);
    }

    /**
     * @param $cp_action_id
     * @param $cp_next_action_id
     * @return mixed
     */
    public function createCpNextAction($cp_action_id, $cp_next_action_id) {
        $cp_next_action = $this->cp_next_actions->createEmptyObject();
        $cp_next_action->cp_action_id = $cp_action_id;
        $cp_next_action->cp_next_action_id = $cp_next_action_id;
        return $this->cp_next_actions->save($cp_next_action);
    }

    public function updateCpNextAction(CpNextAction $cp_next_action) {
        $this->cp_next_actions->save($cp_next_action);
    }

    public function deleteCpNextAction(CpNextAction $cp_next_action) {
        $this->cp_next_actions->deletePhysical($cp_next_action);
    }

    /**
     * 配列で渡されたアクションを直列で繋げる
     * @param $actions
     */
    public function createCpNextActionByActions($actions) {
        $parent_action = array();
        foreach ($actions as $action) {
            if (!$parent_action) {
                $parent_action = $action;
                continue;
            }
            $this->createCpNextAction($parent_action->id, $action->id);

            $parent_action = $action;
        }
    }

    public function deleteCpNextActionsInGroup($cp_actions) {
        foreach($cp_actions as $cp_action) {
            $cp_next_action = $this->getCpNextActionByCpActionId($cp_action->id);
            if(!$cp_next_action) {
                continue;
            }
            $this->deleteCpNextAction($cp_next_action);
        }
    }
}
