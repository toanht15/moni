<?php

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.services.CpTransactionService');
AAFW::import('jp.aainc.classes.services.instant_win.InstantWinPrizeService');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;

/**
 * Class CpInstantWinActionManager
 * TODO トランザクション
 */
class CpInstantWinActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    /** @var CpInstantWinActions $cp_concrete_actions */
    protected $cp_concrete_actions;
    /** @var CpTransactionService $cp_transaction_service */
    protected $cp_transaction_service;
    /** @var InstantWinPrizeService $instant_win_prize_service */
    protected $instant_win_prize_service;
    protected $logger;

    const LOGIC_TYPE_RATE = 1;
    const LOGIC_TYPE_TIME = 2;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_concrete_actions = $this->getModel("CpInstantWinActions");
        $this->cp_transaction_service = $this->getService('CpTransactionService');
        $this->instant_win_prize_service = $this->getService('InstantWinPrizeService');
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
        if($cp_concrete_action == null) {
            $instant_win_prizes = null;
        } else {
            $instant_win_prizes = $this->instant_win_prize_service->getInstantWinPrizesByCpInstantWinActionId($cp_concrete_action->id);
        }
        return array($cp_action, $cp_concrete_action, $instant_win_prizes);
    }

    /**
     * @param $cp_action_group_id
     * @param $type
     * @param $status
     * @param $order_no
     * @return array|mixed
     */
    public function createCpActions($cp_action_group_id, $type, $status, $order_no) {
        $cp_action = $this->createCpAction($cp_action_group_id, $type, $status, $order_no);
        $cp_concrete_action = $this->createConcreteAction($cp_action);
        $this->cp_transaction_service->createCpTransaction($cp_action->id);
        $this->instant_win_prize_service->createInitInstantWinPrizes($cp_concrete_action->id);
        return array($cp_action, $cp_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed|void
     */
    public function deleteCpActions(CpAction $cp_action) {
        $this->cp_transaction_service->deleteCpTransaction($cp_action->id);
        $this->deleteConcreteAction($cp_action);
        $this->deleteCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateCpActions(CpAction $cp_action, $data) {
        $this->updateCpAction($cp_action);
        $cp_concrete_action = $this->updateConcreteAction($cp_action, $data);
        $this->instant_win_prize_service->updateInstantWinPrizes($cp_concrete_action->id, $data);
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
        $cp_concrete_action->cp_action_id     = $cp_action->id;
        $cp_concrete_action->title            = "スピードくじ";
        $cp_concrete_action->text             = "";
        $cp_concrete_action->time_value       = 1;
        $cp_concrete_action->time_measurement = CpInstantWinActions::TIME_MEASUREMENT_DAY;
        $cp_concrete_action->logic_type       = CpInstantWinActions::LOGIC_TYPE_RATE;
        $cp_concrete_action->once_flg         = InstantWinPrizes::ONCE_FLG_ON;
        $this->cp_concrete_actions->save($cp_concrete_action);
        return $cp_concrete_action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        $cp_concrete_action->title            = $data['title'];
        $cp_concrete_action->text             = $data['text'];
        $cp_concrete_action->html_content = Markdown::defaultTransform($data['text']);
        $cp_concrete_action->time_value       = $data['time_value'];
        $cp_concrete_action->time_measurement = $data['time_measurement'];
        $cp_concrete_action->once_flg         = $data['once_flg'];
        $cp_concrete_action->logic_type       = $data['logic_type'];
        $this->cp_concrete_actions->save($cp_concrete_action);
        return $cp_concrete_action;
    }

    /**
     * @param CpAction $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        $cp_concrete_action->del_flg = 1;
        $this->cp_concrete_actions->save($cp_concrete_action);
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed|void
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $new_concrete_action->cp_action_id     = $new_cp_action_id;
        $new_concrete_action->title            = $old_concrete_action->title;
        $new_concrete_action->text             = $old_concrete_action->text;
        $new_concrete_action->html_content     = $old_concrete_action->html_content;
        $new_concrete_action->time_value       = $old_concrete_action->time_value;
        $new_concrete_action->time_measurement = $old_concrete_action->time_measurement;
        $new_concrete_action->once_flg         = $old_concrete_action->once_flg;
        $new_concrete_action->logic_type       = $old_concrete_action->logic_type;
        $new_cp_instant_win_action = $this->cp_concrete_actions->save($new_concrete_action);

        $this->cp_transaction_service->createCpTransaction($new_cp_action_id);
        $this->instant_win_prize_service->copyInstantWinPrizes($new_cp_instant_win_action->id, $old_concrete_action->id);
    }

    /**
     * @param CpAction $cp_action
     * @return entity
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
     * 次回参加までの待機時間を変換する
     * @param $cp_concrete_action
     * @return string
     */
    public function changeValueIntoTime($cp_concrete_action) {
        if ($cp_concrete_action->time_measurement == CpInstantWinActions::TIME_MEASUREMENT_MINUTE) {
            $waiting_time = '+' . $cp_concrete_action->time_value . ' minutes';
        } elseif ($cp_concrete_action->time_measurement == CpInstantWinActions::TIME_MEASUREMENT_HOUR) {
            $waiting_time = '+' . $cp_concrete_action->time_value . ' hours';
        } else {
            $waiting_time = '+' . $cp_concrete_action->time_value . ' days';
        }
        return $waiting_time;
    }

    /**
     * @param CpAction $cp_action
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {
        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpInstantWinActionManager#deletePhysicalRelatedCpActionData cp_action_id=" . $cp_action->id);
        }
        if ($with_concrete_actions) {
            //TODO delete concrete action
        }

        //delete instant win action user log
        /** @var InstantWinUserService $instant_win_user_service */
        $instant_win_user_service = $this->getService("InstantWinUserService");
        $instant_win_user_service->deletePhysicalUserLogsByCpActionId($cp_action->id);

        // 当選予定時刻か初期化
        $cp_concrete_action = $this->getConcreteAction($cp_action);
        $instant_win_prize = $this->instant_win_prize_service->getInstantWinPrizeByPrizeStatus($cp_concrete_action->id, InstantWinPrizes::PRIZE_STATUS_PASS);
        $this->instant_win_prize_service->resetInstantWinTime($instant_win_prize);
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        if (!$cp_action || !$cp_user) {
            throw new Exception("CpInstantWinActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }
        /** @var InstantWinUserService $instant_win_user_service */
        $instant_win_user_service = $this->getService("InstantWinUserService");
        $instant_win_user_service->deletePhysicalUserLogsByCpActionIdAndCpUserId($cp_action->id, $cp_user->id);
    }
}
