<?php

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

/**
 * Class CpShareActionManager
 */

class CpShareActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    protected $cp_share_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_share_actions = $this->getModel("CpShareActions");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_action_id
     * @return array|mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $cp_share_action = null;
        } else {
            $cp_share_action = $this->getCpShareActionByCpAction($cp_action);
        }
        return array($cp_action, $cp_share_action);
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
        $cp_share_action = $this->createConcreteAction($cp_action);
        return array($cp_action, $cp_share_action);
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
        return $this->getCpShareActionByCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $cp_share_action = $this->cp_share_actions->createEmptyObject();
        $cp_share_action->cp_action_id = $cp_action->id;
        $cp_share_action->title = "友達にキャンペーンをおすすめしよう!";
        $cp_share_action->placeholder = "参加しました！";
        $cp_share_action->button_label_text = "シェアして次へ";
        $this->cp_share_actions->save($cp_share_action);
        return $cp_share_action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $cp_share_action = $this->getCpShareActionByCpAction($cp_action);
        $cp_share_action->title = $data["title"];
        $cp_share_action->placeholder = $data["placeholder"];
        $cp_share_action->button_label_text = $data['button_label_text'];
        $cp_share_action->share_url = $data['share_url'];
        $cp_share_action->meta_data = $data['meta_data'];
        $this->cp_share_actions->save($cp_share_action);
    }

    /**
     * @param $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $action = $this->getCpShareActionByCpAction($cp_action);
        $action->del_flg = 1;
        $this->cp_share_actions->save($action);
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_share_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->title = $old_concrete_action->title;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        $new_concrete_action->placeholder = $old_concrete_action->placeholder;
        $new_concrete_action->button_label_text = $old_concrete_action->button_label_text;
        $new_concrete_action->share_url = $old_concrete_action->share_url;
        $new_concrete_action->meta_data = $old_concrete_action->meta_data;
        return $this->cp_share_actions->save($new_concrete_action);
    }

    /**
     * @param $cp_action
     * @return mixed
     */
    public function getCpShareActionByCpAction(CpAction $cp_action) {
        return $this->cp_share_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * @param $cp_action
     * @return mixed
     */
    public function getCpShareActionByCpActionId($cp_action_id) {
        return $this->cp_share_actions->findOne(array("cp_action_id" => $cp_action_id));
    }

    /**
     * @param CpAction $cp_action
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {

        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpShareActionManager#deletePhysicalRelatedCpActionData cp_action_id=" . $cp_action->id);
        }

        if ($with_concrete_actions) {
            //TODO delete concrete action
        }

        //delete shared log
        $concrete_action = $this->getConcreteAction($cp_action);
        /** @var CpShareUserLogService $cp_share_action_log_service */
        $cp_share_action_log_service = $this->getService('CpShareUserLogService');
        $cp_share_action_log_service->deletePhysicalShareUserLogByShareActionId($concrete_action->id);
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        if (!$cp_action || !$cp_user) {
            throw new Exception("CpShareActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        //delete shared log
        $concrete_action = $this->getConcreteAction($cp_action);
        if (!$concrete_action) {
            throw new Exception("CpShareActionManager#deletePhysicalRelatedCpActionDataByCpUser concrete action null cp_action_id=" . $cp_action->id);
        }
        /** @var CpShareUserLogService $cp_share_action_log_service */
        $cp_share_action_log_service = $this->getService('CpShareUserLogService');
        $cp_share_action_log_service->deletePhysicalShareUserLogByShareActionIdAndCpUserId($concrete_action->id, $cp_user->id);
    }
}