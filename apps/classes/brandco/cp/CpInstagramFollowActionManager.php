<?php
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');
AAFW::import('jp.aainc.classes.service.CpInstagramFollowEntryService');

/**
* Class CpInstagramFollowActionManager
*/
class CpInstagramFollowActionManager extends aafwObject implements CpActionManager {
    use CpActionTrait;

    /** @var  CpInstagramFollowActions $cp_concrete_actions */
    protected $cp_concrete_actions;
    /** @var  CpInstagramFollowEntryService $cp_ig_follow_entry_service */
    protected $cp_ig_follow_entry_service;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_concrete_actions = $this->getModel("CpInstagramFollowActions");
        $this->cp_ig_follow_entry_service = $this->getService("CpInstagramFollowEntryService");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $cp_concrete_action = null;
        } else {
            $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        }
        return array($cp_action, $cp_concrete_action);
    }

    /**
     * @param $cp_action_group_id
     * @param $type
     * @param $status
     * @param $order_no
     * @return mixed
     */
    public function createCpActions($cp_action_group_id, $type, $status, $order_no) {
        $cp_action = $this->createCpAction($cp_action_group_id, $type, $status, $order_no);
        $cp_concrete_action = $this->createConcreteAction($cp_action);
        return array($cp_action, $cp_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function deleteCpActions(CpAction $cp_action) {
        $this->deleteConcreteAction($cp_action);
        $this->deleteCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed
     */
    public function updateCpActions(CpAction $cp_action, $data) {
        $this->updateCpAction($cp_action);
        $this->updateConcreteAction($cp_action, $data);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function getConcreteAction(CpAction $cp_action) {
        return $this->getCpConcreteActionByCpAction($cp_action);
    }

    /**
     * @param $cp_action_id
     * @return entity
     */
    public function getCpConcreteActionByCpActionId($cp_action_id) {
        return $this->cp_concrete_actions->findOne(array("cp_action_id" => $cp_action_id));
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $cp_concrete_action->cp_action_id = $cp_action->id;
        $cp_concrete_action->title = "Instagram フォロー";
        $this->cp_concrete_actions->save($cp_concrete_action);
        return $cp_concrete_action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        $this->cp_concrete_actions->save($cp_concrete_action);

        $this->updateCpConcreteActionEntry($cp_concrete_action->id, $data);
    }

    /**
     * @param $cp_concrete_action_id
     * @param $data
     */
    public function updateCpConcreteActionEntry($cp_concrete_action_id, $data) {
        $cp_concrete_action_entry = $this->cp_ig_follow_entry_service->getTargetAccount($cp_concrete_action_id);
        if ($cp_concrete_action_entry) {
            $this->cp_ig_follow_entry_service->update($cp_concrete_action_id, $data['brand_social_account_id'], $data['instagram_entry_id']);
        } else {
            $this->cp_ig_follow_entry_service->create($cp_concrete_action_id, $data['brand_social_account_id'], $data['instagram_entry_id']);
        }
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        $cp_concrete_action->del_flg = 1;
        $this->cp_concrete_actions->save($cp_concrete_action);
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->title = $old_concrete_action->title;
        $new_concrete_action->skip_flg = $old_concrete_action->skip_flg;
        $this->cp_concrete_actions->save($new_concrete_action);
    }

    /**
     * cp_concrete_action取得
     * @param $cp_action
     * @return mixed
     */
    private function getCpConcreteActionByCpAction(CpAction $cp_action) {
        return $this->cp_concrete_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {

        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpInstagramFollowActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        if ($with_concrete_actions) {
            //TODO delete concrete action
        }

        /** @var CpInstagramFollowUserLogService $cp_ig_follow_user_log_service */
        $cp_ig_follow_user_log_service = $this->getService("CpInstagramFollowUserLogService");
        $cp_ig_follow_user_log_service->deletePhysicalInstagramFlowUserLogsByCpActionId($cp_action->id);

        /** @var CpInstagramFollowActionLogService $cp_ig_follow_action_log_service */
        $cp_ig_follow_action_log_service = $this->getService("CpInstagramFollowActionLogService");
        $cp_ig_follow_action_log_service->deletePhysicalInstagramFollowActionLogsByCpActionId($cp_action->id);
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        if (!$cp_action || !$cp_user) {
            throw new Exception("CpInstagramFollowActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        /** @var CpInstagramFollowUserLogService $cp_ig_follow_user_log_service */
        $cp_ig_follow_user_log_service = $this->getService("CpInstagramFollowUserLogService");
        $cp_ig_follow_user_log_service->deletePhysicalInstagramFlowUserLogsByCpActionIdAndCpUserId($cp_action->id, $cp_user->id);

        /** @var CpInstagramFollowActionLogService $cp_ig_follow_action_log_service */
        $cp_ig_follow_action_log_service = $this->getService("CpInstagramFollowActionLogService");
        $cp_ig_follow_action_log_service->deletePhysicalInstagramFollowActionLogsByCpActionIdAndCpUserId($cp_action->id, $cp_user->id);
    }
}
