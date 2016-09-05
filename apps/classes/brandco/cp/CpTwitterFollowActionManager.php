<?php
/**
 * User: t-yokoyama
 * Date: 15/03/10
 * Time: 13:32
 */

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

class CpTwitterFollowActionManager extends aafwObject implements CpActionManager {
    use CpActionTrait;

    const FOLLOW_ACTION_UNREAD      = '0';    // TWitter未連携
    const FOLLOW_ACTION_EXEC        = '1';    // フォローする
    const FOLLOW_ACTION_ALREADY     = '2';    // フォロー済み
    const FOLLOW_ACTION_SKIP        = '3';    // フォロースキップ
    const FOLLOW_ACTION_CLOSE       = '4';    // 実行済み
    const FOLLOW_ACTION_CONNECTING  = '5';    // 連携後

    const FOLLOW_FORM_TITLE =
        '「%s」のTwitterアカウントをフォローしよう！';

    const SOCIAL_TYPE_STRING = 'Twitter';

    /** @var CpTwitterFollowActions $cp_concrete_actions */
    protected $cp_concrete_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_concrete_actions = $this->getModel("CpTwitterFollowActions");
        $this->brands = $this->getModel("brands");
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
        $cp_concrete_action->title = '';
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
        $cp_concrete_action->del_flg = 0;
        $cp_concrete_action->title = $data['title'];
        $cp_concrete_action->skip_flg = $data['skip_flg'];
        $this->cp_concrete_actions->save($cp_concrete_action);
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
     * cp_concrete_action取得
     * @param $cp_action
     * @return mixed
     */
    public function getCpConcreteActionByCpAction(CpAction $cp_action) {
        return $this->cp_concrete_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * cp_concrete_action取得
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpConcreteActionByCpActionId($cp_action_id) {
        return $this->cp_concrete_actions->findOne(array("cp_action_id" => $cp_action_id));
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $cp_tw_follow_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->title = $cp_tw_follow_action->title;
        $new_concrete_action->skip_flg = $cp_tw_follow_action->skip_flg;
        $this->cp_concrete_actions->save($new_concrete_action);

        //copy engagement_tw_social_accounts
        /** @var CpTwitterFollowAccountService $cp_tw_follow_account_service */
        $cp_tw_follow_account_service = $this->_ServiceFactory->create('CpTwitterFollowAccountService');
        $engagement_tw_social_account =
            $cp_tw_follow_account_service->getFollowTargetSocialAccount($cp_tw_follow_action->id);
        $cp_tw_follow_account_service->create(
            $new_concrete_action->id,
            $engagement_tw_social_account->brand_social_account_id
        );
    }

    /**
     * アクションタイトルを生成
     *
     * @param $brand_social_account_name
     * @return string
     */
    public function makeFollowActionTitle($brand_social_account_name) {
        $title = sprintf(
            self::FOLLOW_FORM_TITLE, $brand_social_account_name
        );

        return $title;
    }

    /**
     * @param CpAction $cp_action
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {

        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpTwitterFollowActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        if ($with_concrete_actions) {
            //TODO delete concrete action
        }

        $concrete_action = $this->getConcreteAction($cp_action);
        if (!$concrete_action) {
            throw new Exception("CpTwitterFollowActionManager#deletePhysicalRelatedCpActionData cant find concrete action cp_action_id=".$cp_action->id);
        }
        /** @var CpTwitterFollowLogService $cp_follow_log_service */
        $cp_follow_log_service = $this->getService("CpTwitterFollowLogService");
        $cp_follow_log_service->deletePhysicalFollowLogsByConcreteActionId($concrete_action->id);
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        if (!$cp_action || !$cp_user) {
            throw new Exception("CpTwitterFollowActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        $concrete_action = $this->getConcreteAction($cp_action);
        if (!$concrete_action) {
            throw new Exception("CpTwitterFollowActionManager#deletePhysicalRelatedCpActionDataByCpUser cant find concrete action cp_action_id=".$cp_action->id);
        }
        /** @var CpTwitterFollowLogService $cp_follow_log_service */
        $cp_follow_log_service = $this->getService("CpTwitterFollowLogService");
        $cp_follow_log_service->deletePhysicalFollowLogsByConcreteActionIdAndCpUserId($concrete_action->id, $cp_user->id);
    }
}
