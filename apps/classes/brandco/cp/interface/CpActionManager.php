<?php

AAFW::import('jp.aainc.classes.CpInfoContainer');

interface CpActionManager {

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpActions($cp_action_id);

    /**
     * @param $cp_action_group_id
     * @param $type
     * @param $status
     * @param $order_no
     * @return mixed
     */
    public function createCpActions($cp_action_group_id, $type, $status, $order_no);

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function deleteCpActions(CpAction $cp_action);

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed
     */
    public function updateCpActions(CpAction $cp_action, $data);

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function getConcreteAction(CpAction $cp_action);

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action);

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed
     */
    public function updateConcreteAction(CpAction $cp_action, $data);

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function deleteConcreteAction(CpAction $cp_action);

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpActionById($cp_action_id);

    /**
     * @param $cp_action_group_id
     * @param $type
     * @param $status
     * @param $order_no
     * @return mixed
     */
    public function createCpAction($cp_action_group_id, $type, $status, $order_no);

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function updateCpAction(CpAction $cp_action);

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function deleteCpAction(CpAction $cp_action);

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id);

    /**
     * @param CpAction $cp_action
     * @param bool $with_concrete_actions
     * @return mixed
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false);

    /**
     * @param CpAction $cp_action
     * @param CpUser $cp_user
     * @return mixed
     */
    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user);


}
