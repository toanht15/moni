<?php

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;

/**
 * Class CpShippingAddressActionManager
 * TODO トランザクション
 */
class CpShippingAddressActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    protected $cp_shipping_address_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_shipping_address_actions = $this->getModel("CpShippingAddressActions");
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
            $entry_action = $this->getCpShippingAddressActionByCpAction($cp_action);
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
        return $this->getCpShippingAddressActionByCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $action = $this->cp_shipping_address_actions->createEmptyObject();
        $action->cp_action_id = $cp_action->id;
        $action->title = "配送先情報を入力してください";
        $action->text = "配送先情報を入力してください。";
        $action->button_label_text = "送信";
        $this->cp_shipping_address_actions->save($action);
        return $action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $action = $this->getCpShippingAddressActionByCpAction($cp_action);
        $action->image_url = $data["image_url"];
        $action->text = $data["text"];
        $action->html_content = Markdown::defaultTransform($data['text']);
        $action->button_label_text = $data["button_label_text"];
        $action->name_required = $data["name_required"];
        $action->address_required = $data["address_required"];
        $action->tel_required = $data["tel_required"];
        $action->title = $data['title'];
        $action->del_flg = 0;
        $this->cp_shipping_address_actions->save($action);
    }

    /**
     * @param $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $action = $this->getCpShippingAddressActionByCpAction($cp_action);
        $action->del_flg = 1;
        $this->cp_shipping_address_actions->save($action);
    }

    /**
     * @param $cp_action
     * @return mixed
     */
    private function getCpShippingAddressActionByCpAction(CpAction $cp_action) {
        return $this->cp_shipping_address_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_shipping_address_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        $new_concrete_action->image_url = $old_concrete_action->image_url;
        $new_concrete_action->text = $old_concrete_action->text;
        $new_concrete_action->title = $old_concrete_action->title;
        $new_concrete_action->button_label_text = $old_concrete_action->button_label_text;
        return $this->cp_shipping_address_actions->save($new_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {

        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpShippingAddressActionManager#deletePhysicalRelatedCpActionData cp_action_id=" . $cp_action->id);
        }

        if ($with_concrete_actions) {
            //TODO delete concrete action
        }

        $concrete_action = $this->getConcreteAction($cp_action);
        if (!$concrete_action) {
            throw new Exception("CpShippingAddressActionManager#deletePhysicalRelatedCpActionData cant find concrete action cp_action_id=".$cp_action->id);
        }
        /** @var ShippingAddressUserService $shipping_address_user_service */
        $shipping_address_user_service = $this->getService("ShippingAddressUserService");
        $shipping_address_user_service->deletePhysicalShippingAddressUserByCpShippingAddressActionId($concrete_action->id);
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        if (!$cp_action || !$cp_user) {
            throw new Exception("CpShippingAddressActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        $concrete_action = $this->getConcreteAction($cp_action);
        if (!$concrete_action) {
            throw new Exception("CpShippingAddressActionManager#deletePhysicalRelatedCpActionData cant find concrete action cp_action_id=".$cp_action->id);
        }
        /** @var ShippingAddressUserService $shipping_address_user_service */
        $shipping_address_user_service = $this->getService("ShippingAddressUserService");
        $shipping_address_user_service->deletePhysicalShippingAddressUserByCpShippingAddressActionIdAndCpUserId($concrete_action->id, $cp_user->id);
    }
}
