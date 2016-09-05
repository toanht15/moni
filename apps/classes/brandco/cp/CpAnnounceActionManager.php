<?php

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;

/**
 * Class CpAnnounceActionManager
 * TODO トランザクション
 */
class CpAnnounceActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    protected $cp_announce_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_announce_actions = $this->getModel("CpAnnounceActions");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_action_id
     * @return array|mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $entry_action = null;
        } else {
            $entry_action = $this->getCpAnnounceActionByCpAction($cp_action);
        }
        return array($cp_action, $entry_action);

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
        $action = $this->createConcreteAction($cp_action);
        return array($cp_action, $action);
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
        return $this->getCpAnnounceActionByCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $action = $this->cp_announce_actions->createEmptyObject();
        $action->cp_action_id = $cp_action->id;
        $action->title = 'ご当選おめでとうございます！';
        $action->text = <<<EOS
この度はキャンペーンにご参加いただきありがとうございました。
ご参加いただいた方の中から、あなたを当選者とさせていただきました。
おめでとうございます！

賞品のお届けは　＜＜例：○月上旬＞＞　頃を予定しております。今しばらくお待ちくださいますようお願いいたします。

EOS;
        $this->cp_announce_actions->save($action);
        return $action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $action = $this->getCpAnnounceActionByCpAction($cp_action);
        $action->image_url = $data["image_url"];
        $action->text = $data["text"];
        $action->html_content = Markdown::defaultTransform($data["text"]);
        $action->title = $data["title"];
        $action->design_type = $data['design_type'];
        $action->del_flg = 0;
        $this->cp_announce_actions->save($action);
    }

    /**
     * @param $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $action = $this->getCpAnnounceActionByCpAction($cp_action);
        $action->del_flg = 1;
        $this->cp_announce_actions->save($action);
    }

    /**
     * @param $cp_action
     * @return mixed
     */
    private function getCpAnnounceActionByCpAction(CpAction $cp_action) {
        return $this->cp_announce_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_announce_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->image_url = $old_concrete_action->image_url;
        $new_concrete_action->text = $old_concrete_action->text;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        $new_concrete_action->title = $old_concrete_action->title;
        $new_concrete_action->design_type = $old_concrete_action->design_type;
        if (!$new_concrete_action->text) {
            $new_concrete_action->text = <<<EOS
この度はキャンペーンにご参加いただきありがとうございました。
ご参加いただいた方の中から、あなたを当選者とさせていただきました。
おめでとうございます！

賞品のお届けは　＜＜例：○月上旬＞＞　頃を予定しております。今しばらくお待ちくださいますようお願いいたします。

EOS;
        }
        if (!$old_concrete_action->html_content) {
            $new_concrete_action->html_content = Markdown::defaultTransform($new_concrete_action->text);
        }
        return $this->cp_announce_actions->save($new_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @param bool $with_concrete_actions
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {
        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpAnnounceActionManager#deletePhysicalRelatedCpActionData cp_action_id=".$cp_action->id);
        }
        if ($with_concrete_actions) {
            $cp_concrete_action = $this->getCpAnnounceActionByCpAction($cp_action);
            if ($cp_concrete_action) {
                $this->cp_announce_actions->deletePhysical($cp_concrete_action);
            }
        }
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        //ユーザーと関係情報がないので実施しない
    }
}
