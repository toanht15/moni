<?php

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;

/**
 * Class CpJoinFinishActionManager
 */
class CpJoinFinishActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    protected $cp_join_finish_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_join_finish_actions = $this->getModel("CpJoinFinishActions");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_action_id
     * @return array|mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $join_finish_action = null;
        } else {
            $join_finish_action = $this->getCpJoinFinishActionByCpAction($cp_action);
        }
        return array($cp_action, $join_finish_action);

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
        return $this->getCpJoinFinishActionByCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $action = $this->cp_join_finish_actions->createEmptyObject();
        $action->cp_action_id = $cp_action->id;
        $action->title = 'ご参加いただきありがとうございました！';
        $action->text = "当選者の方には別途当選通知をお送りいたします。";
        $action->cv_tag = '';
        $this->cp_join_finish_actions->save($action);
        return $action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $action = $this->getCpJoinFinishActionByCpAction($cp_action);
        $action->text = $data["text"];
        $action->html_content = Markdown::defaultTransform($data["text"]);
        $action->cv_tag = $data["cv_tag"];
        $action->title = $data["title"];
        $action->design_type = $data['design_type'];
        $action->image_url = $data['image_url'];
        $action->del_flg = 0;
        $this->cp_join_finish_actions->save($action);
    }

    /**
     * @param $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $action = $this->getCpJoinFinishActionByCpAction($cp_action);
        $action->del_flg = 1;
        $this->cp_join_finish_actions->save($action);
    }

    /**
     * @param $cp_action
     * @return mixed
     */
    private function getCpJoinFinishActionByCpAction(CpAction $cp_action) {
        return $this->cp_join_finish_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_join_finish_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->text = $old_concrete_action->text;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        $new_concrete_action->title = $old_concrete_action->title;
        $new_concrete_action->cv_tag = "";
        $new_concrete_action->design_type = $old_concrete_action->design_type;
        if (!$new_concrete_action->text) {
            $new_concrete_action->text = "当選者の方には別途当選通知をお送りいたします。";
        }
        if (!$new_concrete_action->html_content) {
            $new_concrete_action->html_content = Markdown::defaultTransform($new_concrete_action->text);
        }
        return $this->cp_join_finish_actions->save($new_concrete_action);
    }

    /**
     * CpAction に関連するデータを削除する
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {
        if ($with_concrete_actions) {
            $concrete_action = $this->getCpJoinFinishActionByCpAction($cp_action);
            $this->cp_join_finish_actions->deletePhysical($concrete_action);
        }
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {}
}
