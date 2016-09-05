<?php

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;

/**
 * Class CpButtonsActionManager
 * TODO トランザクション
 */
class CpButtonsActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    protected $cp_buttons_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        /** @var CpFlowService cp_flow_service */
        $this->cp_flow_service = $this->_ServiceFactory->create('CpFlowService');

        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_buttons_actions = $this->getModel("CpButtonsActions");
        $this->cp_next_action = $this->getModel("CpNextActions");
        $this->cp_next_action_info = $this->getModel("CpNextActionInfos");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_action_id
     * @return array|mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $buttons_action = null;
        } else {
            $buttons_action = $this->getCpButtonsActionByCpAction($cp_action);
        }
        return array($cp_action, $buttons_action);

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
        $buttons_action = $this->createConcreteAction($cp_action);
        return array($cp_action, $buttons_action);
    }

    /**
     * @param $cp_action_id
     * @param array $data
     */
    public function createCpNextActionAndInfo($cp_action_id, $data=array()){
        $cp_next_action = $this->cp_flow_service->createCpNextAction($cp_action_id, $data['cp_next_action_id']);
        $data['cp_next_action_table_id'] = $cp_next_action->id;
        $this->createNextActionInfo($data);
    }

    public function deleteCpNextActionAndInfo(CpAction $cp_action) {
        $cp_next_actions = $cp_action->getCpNextActions();
        if (!$cp_next_actions) return;

        foreach ($cp_next_actions as $cp_next_action) {
            $cp_next_action_info = $this->getNextActionInfoByCpNextAction($cp_next_action);
            if ($cp_next_action_info) {
                $this->deleteCpNextActionInfo($cp_next_action_info);
            }
            $this->cp_flow_service->deleteCpNextAction($cp_next_action);
        }
    }

    /**
     * @param CpNextActionInfo $cp_next_action_info
     */
    public function deleteCpNextActionInfo(CpNextActionInfo $cp_next_action_info) {
        $this->cp_next_action_info->deletePhysical($cp_next_action_info);
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
        return $this->getCpButtonsActionByCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $buttons_action = $this->cp_buttons_actions->createEmptyObject();
        $buttons_action->cp_action_id = $cp_action->id;
        $buttons_action->title = "選択してください";
        $buttons_action->text = "";
        $this->cp_buttons_actions->save($buttons_action);

        return $buttons_action;
    }

    /**
     * @param $data
     */
    public function createNextActionInfo($data) {
        $button = $this->cp_next_action_info->createEmptyObject();
        $button->next_action_table_id = $data['cp_next_action_table_id'];
        $button->label = $data['label'];
        $button->order_no = $data['order'];
        return $this->cp_next_action_info->save($button);
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $buttons_action = $this->getCpButtonsActionByCpAction($cp_action);
        $buttons_action->image_url = $data["image_url"];
        $buttons_action->text = $data["text"];
        $buttons_action->html_content = $data['html_content'];
        $buttons_action->html_content = Markdown::defaultTransform($data["text"]);
        $buttons_action->title = $data['title'];
        $buttons_action->del_flg = 0;
        return $this->cp_buttons_actions->save($buttons_action);
    }

    /**
     * @param $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $buttons_action = $this->getCpButtonsActionByCpAction($cp_action);
        $buttons_action->del_flg = 1;
        $this->cp_buttons_actions->save($buttons_action);
    }

    /**
     * @param $cp_action
     * @return mixed
     */
    private function getCpButtonsActionByCpAction(CpAction $cp_action) {
        return $this->cp_buttons_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * @param CpNextAction $cp_next_action
     * @return mixed
     */
    public function getNextActionInfoByCpNextAction(CpNextAction $cp_next_action) {
        $filter = array(
            'conditions' => array(
                'next_action_table_id' => $cp_next_action->id
            )
        );
        return $this->cp_next_action_info->findOne($filter);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getNextActionInfoById($id) {
        return $this->cp_next_action_info->findOne($id);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function getNextActionsInfo(CpAction $cp_action) {
        $cp_next_actions = $cp_action->getCpNextActions();
        $next_action_id = array();
        foreach ($cp_next_actions as $cp_next_action) {
            $next_action_id[] = $cp_next_action->id;
        }
        if (count($next_action_id) == 0) {
            return null;
        }
        $filter = array(
            'conditions' => array(
                'next_action_table_id' => $next_action_id
            ),
            'order' => array(
                'name' => 'order_no'
            )
        );
        return $this->cp_next_action_info->find($filter);
    }

    public function getAfterActions(CpAction $cpAction, $cp_id) {

        $after_action = $this->cp_flow_service->getAfterActions($cpAction);

        if ($after_action) {
            $after_action = $after_action->toArray();
        } else {
            $after_action = array();
        }

        $after_action_groups = $this->cp_flow_service->getAfterActionGroups($cp_id, $cpAction->cp_action_group_id);

        if ($after_action_groups) {
            foreach ($after_action_groups as $action_group) {
                $actions = $this->cp_flow_service->getCpActionsByCpActionGroupId($action_group->id);
                if ($actions) {
                    $after_action = array_merge($after_action, $actions->toArray());
                }
            }
        }

        return $after_action;
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_buttons_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->image_url = $old_concrete_action->image_url;
        $new_concrete_action->text = $old_concrete_action->text;
        $new_concrete_action->title = $old_concrete_action->title;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        return $this->cp_buttons_actions->save($new_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @param bool $with_concrete_actions
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {
        // TODO: Implement deleteRelatedCpActionData() method.
        //ユーザーと関係情報がないので実施しない
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        // TODO: Implement deletePhysicalRelatedCpActionDataByCpUser() method.
        //ユーザーと関係情報がないので実施しない
    }
}
