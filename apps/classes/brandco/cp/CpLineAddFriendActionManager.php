<?php
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

/**
 * Class CpLineAddFriendActionManager
 * このテープルcp_line_add_friend_actionsに関する操作（Insert、Update、Delete）をここで実施する
 */
class CpLineAddFriendActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    /** @var  CpLineAddFriendActions $cp_concrete_action_store */
    protected $cp_concrete_action_store;
    protected $logger;

    function __construct() {

        parent::__construct();

        /** @var CpActions cp_actions */
        $this->cp_actions = $this->getModel("CpActions");

        /** @var CpLineAddFriendActions cp_concrete_action_store */
        $this->cp_concrete_action_store = $this->getModel("CpLineAddFriendActions");

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * CP_ACTIONやCpLineAddFriendActionを検索
     * @param $cp_action_id
     * @return array
     */
    public function getCpActions($cp_action_id) {

        $cp_action = $this->getCpActionById($cp_action_id);

        $cp_concrete_action = null;

        if ($cp_action !== null) {
            $cp_concrete_action = $this->getConcreteAction($cp_action);
        }

        return array($cp_action, $cp_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @return CpLineAddFriendAction
     */
    public function getConcreteAction(CpAction $cp_action) {
        return $this->cp_concrete_action_store->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * @param $cp_action_group_id
     * @param $type
     * @param $status
     * @param $order_no
     * @return array
     */
    public function createCpActions($cp_action_group_id, $type, $status, $order_no) {

        $cp_action = $this->createCpAction($cp_action_group_id, $type, $status, $order_no);

        $cp_concrete_action = $this->createConcreteAction($cp_action);

        return array($cp_action, $cp_concrete_action);
    }

    /**
     * cp_line_add_friend_actionsに新いレコードを追加
     * @param CpAction $cp_action
     * @return CpLineAddFriendAction
     */
    public function createConcreteAction(CpAction $cp_action) {

        $cp_concrete_action = $this->cp_concrete_action_store->createEmptyObject();
        $cp_concrete_action->cp_action_id = $cp_action->id;
        $cp_concrete_action->comment = 'あなたのLINEに最新情報をお届けします。是非友だちに追加してください！';

        $this->cp_concrete_action_store->save($cp_concrete_action);

        return $cp_concrete_action;
    }

    /**
     * cp_actionsやcp_line_add_friend_actionsを更新
     * @param CpAction $cp_action
     * @param $data
     */
    public function updateCpActions(CpAction $cp_action, $data) {
        $this->updateCpAction($cp_action);
        $this->updateConcreteAction($cp_action, $data);
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $cp_concrete_action = $this->getConcreteAction($cp_action);

        $cp_concrete_action->title = $data['title'];
        $cp_concrete_action->line_account_id = $data['line_account_id'];
        $cp_concrete_action->line_account_name = $data['line_account_name'];
        $cp_concrete_action->comment = $data['comment'];
        $cp_concrete_action->del_flg = 0;

        $this->cp_concrete_action_store->save($cp_concrete_action);
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);

        $new_concrete_action = $this->cp_concrete_action_store->createEmptyObject();

        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->title = $old_concrete_action->title;
        $new_concrete_action->line_account_name = $old_concrete_action->line_account_name;
        $new_concrete_action->line_account_id = $old_concrete_action->line_account_id;
        $new_concrete_action->comment = $old_concrete_action->comment;

        $this->cp_concrete_action_store->save($new_concrete_action);
    }


    /**
     * @param CpAction $cp_action
     */
    public function deleteCpActions(CpAction $cp_action) {
        $this->deleteConcreteAction($cp_action);
        $this->deleteCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->getConcreteAction($cp_action);
        $cp_concrete_action->del_flg = 1;
        $this->cp_concrete_action_store->save($cp_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @param bool $with_concrete_actions
     * @throws Exception
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {

        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpLineAddFriendActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        $concrete_action = $this->getConcreteAction($cp_action);

        /** @var CpLineAddFriendActionLogService $cp_line_add_friend_action_log_service */
        $cp_line_add_friend_action_log_service = $this->getService('CpLineAddFriendActionLogService');
        $cp_line_add_friend_action_log_service->deletePhysicalLogByCpActionId($concrete_action->id);
    }

    /**
     * @param CpAction $cp_action
     * @param CpUser $cp_user
     * @throws Exception
     */
    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {

        if (!$cp_action || !$cp_user) {
            throw new Exception("CpLineAddFriendActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        $concrete_action = $this->getConcreteAction($cp_action);

        /** @var CpLineAddFriendActionLogService $cp_line_add_friend_action_log_service */
        $cp_line_add_friend_action_log_service = $this->getService('CpLineAddFriendActionLogService');
        $cp_line_add_friend_action_log_service->deletePhysicalLogsByCpActionIdAndCpUserId($concrete_action->id, $cp_user->id);
    }
}
