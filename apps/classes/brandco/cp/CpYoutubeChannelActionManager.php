<?php
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

/**
* Class CpYoutubeChannelActionManager
*/
class CpYoutubeChannelActionManager extends aafwObject implements CpActionManager {
    use CpActionTrait;

    /** @var  CpYoutubeChannelActions $cp_concrete_actions */
    protected $cp_concrete_actions;
    /** @var  CpYoutubeChannelAccounts $cp_concrete_accounts */
    protected $cp_concrete_accounts;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_concrete_actions = $this->getModel("CpYoutubeChannelActions");
        $this->cp_concrete_accounts = $this->getModel("CpYoutubeChannelAccounts");
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
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $cp_concrete_action->cp_action_id = $cp_action->id;
        $cp_concrete_action->title = "YouTubeチャンネル登録";
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
        $cp_concrete_action->intro_flg = $data['intro_flg'];
        $this->cp_concrete_actions->save($cp_concrete_action);

        $cp_concrete_account = $this->getCpYoutubeChannelAccount($cp_concrete_action);
        if (!$cp_concrete_account) {
            $cp_concrete_account = $this->cp_concrete_accounts->createEmptyObject();
            $cp_concrete_account->cp_youtube_channel_action_id = $cp_concrete_action->id;
        }
        $cp_concrete_account->brand_social_account_id = $data['brand_social_account_id'];
        $cp_concrete_account->youtube_entry_id = $data['youtube_entry_id'];
        $this->cp_concrete_accounts->save($cp_concrete_account);
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
        $new_concrete_action->intro_flg = $old_concrete_action->intro_flg;
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

    /**
     * @param $cp_action_id
     * @return entity
     */
    public function getCpConcreteActionByCpActionId($cp_action_id) {
        return $this->cp_concrete_actions->findOne(array("cp_action_id" => $cp_action_id));
    }

    /**
     * @param $cp_concrete_action
     * @return entity
     */
    private function getCpYoutubeChannelAccount($cp_concrete_action) {
        return $this->cp_concrete_accounts->findOne(array("cp_youtube_channel_action_id" => $cp_concrete_action->id));
    }

    /**'
     * @param CpAction $cp_action
     * @param bool $with_concrete_actions
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {

        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpYoutubeChannelActionManager#deletePhysicalRelatedCpActionData cp_action_id=" . $cp_action->id);
        }

        /** @var CpYoutubeChannelUserLogService $cp_yt_channel_user_log_service */
        $cp_yt_channel_user_log_service = $this->getService("CpYoutubeChannelUserLogService");
        $cp_yt_channel_user_log_service->deletePhysicalYoutubeChannelUserLogsByCpActionId($cp_action->id);
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        if (!$cp_action || !$cp_user) {
            throw new Exception("CpYoutubeChannelActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }
        /** @var CpYoutubeChannelUserLogService $cp_yt_channel_user_log_service */
        $cp_yt_channel_user_log_service = $this->getService("CpYoutubeChannelUserLogService");
        $cp_yt_channel_user_log_service->deletePhysicalYoutubeChannelUserLogsByCpActionIdAndCpUserId($cp_action->id, $cp_user->id);
    }
}
