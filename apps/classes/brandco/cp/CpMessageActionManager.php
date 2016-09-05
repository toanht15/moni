<?php

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;

/**
 * Class CpMessageActionManager
 * TODO トランザクション
 */
class CpMessageActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    protected $cp_message_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_message_actions = $this->getModel("CpMessageActions");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_action_id
     * @return array|mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $cp_message_action = null;
        } else {
            $cp_message_action = $this->getCpMessageActionByCpAction($cp_action);
        }
        return array($cp_action, $cp_message_action);

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
        $cp_message_action = $this->createConcreteAction($cp_action);
        return array($cp_action, $cp_message_action);
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
        return $this->getCpMessageActionByCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $cp_message_action = $this->cp_message_actions->createEmptyObject();
        $cp_message_action->cp_action_id = $cp_action->id;
        $cp_message_action->title = "メッセージ";
        $cp_message_action->text = "";
        $cp_message_action->manual_step_flg = 0;
        $cp_message_action->send_text_mail_flg = 0;
        $this->cp_message_actions->save($cp_message_action);
        return $cp_message_action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $cp_message_action = $this->getCpMessageActionByCpAction($cp_action);
        $cp_message_action->image_url = $data["image_url"];
        $cp_message_action->text = $data["text"];
        $cp_message_action->html_content = Markdown::defaultTransform($data['text']);
        $cp_message_action->title = $data['title'];
        $cp_message_action->manual_step_flg = $data['manual_step_flg'];
        $cp_message_action->send_text_mail_flg = $data['send_text_mail_flg'];
        $cp_message_action->del_flg = 0;
        $this->cp_message_actions->save($cp_message_action);
    }

    /**
     * @param $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $cp_message_action = $this->getCpMessageActionByCpAction($cp_action);
        $cp_message_action->del_flg = 1;
        $this->cp_message_actions->save($cp_message_action);
    }

    /**
     * @param $cp_action
     * @return mixed
     */
    private function getCpMessageActionByCpAction(CpAction $cp_action) {
        return $this->cp_message_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_message_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->image_url = $old_concrete_action->image_url;
        $new_concrete_action->text = $old_concrete_action->text;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        $new_concrete_action->title = $old_concrete_action->title;
        $new_concrete_action->manual_step_flg = $old_concrete_action->manual_step_flg;
        $new_concrete_action->send_text_mail_flg = $old_concrete_action->send_text_mail_flg;
        return $this->cp_message_actions->save($new_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @param bool $with_concrete_actions
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {
        if ($with_concrete_actions) {
            $cp_concrete_action = $this->getCpMessageActionByCpAction($cp_action);
            $this->cp_message_actions->deletePhysical($cp_concrete_action);
        }
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        //ユーザーと関係情報がないので実施しない
    }
}
