<?php
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;
/**
* Class CpGiftActionManager
*/
class CpGiftActionManager extends aafwObject implements CpActionManager {
    use CpActionTrait;

    /** @var CpGifActions $cp_concrete_actions */
    protected $cp_concrete_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions               = $this->getModel("CpActions");
        $this->cp_concrete_actions      = $this->getModel("CpGiftActions");
        $this->brand_social_accounts    = $this->getModel("BrandSocialAccounts");
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
        $cp_action          = $this->createCpAction($cp_action_group_id, $type, $status, $order_no);
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
        $cp_concrete_action->cp_action_id           = $cp_action->id;
        $cp_concrete_action->title                  = 'ギフト';
        $cp_concrete_action->button_label_text      = '作成する';
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
        $cp_concrete_action->del_flg            = 0;
        $cp_concrete_action->title              = $data['title'];
        $cp_concrete_action->image_url          = $data['image_url'];
        $cp_concrete_action->text               = $data['text'];
        $cp_concrete_action->html_content       = Markdown::defaultTransform($data['text']);
        $cp_concrete_action->receiver_text      = $data['receiver_text'];
        $cp_concrete_action->button_label_text  = $data['button_label_text'];
        $cp_concrete_action->card_required      = $data['card_required'];
        $cp_concrete_action->incentive_type     = $data['incentive_type'];
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
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $cp_gift_action         = $this->getConcreteAction($old_cp_action);
        $new_concrete_action    = $this->cp_concrete_actions->createEmptyObject();
        $new_concrete_action->cp_action_id          = $new_cp_action_id;
        $new_concrete_action->title                 = $cp_gift_action->title;
        $new_concrete_action->image_url             = $cp_gift_action->image_url;
        $new_concrete_action->text                  = $cp_gift_action->text;
        $new_concrete_action->html_content          = Markdown::defaultTransform($cp_gift_action->text);
        $new_concrete_action->receiver_text         = $cp_gift_action->receiver_text;
        $new_concrete_action->button_label_text     = $cp_gift_action->button_label_text;
        $new_concrete_action->card_required         = $cp_gift_action->card_required;
        $new_concrete_action->incentive_type        = $cp_gift_action->incentive_type;
        $this->cp_concrete_actions->save($new_concrete_action);

    }

    /**
     * @param CpAction $cp_action
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {

        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpGiftActionManager#deletePhysicalRelatedCpActionData cp_action_id=" . $cp_action->id);
        }

        if ($with_concrete_actions) {
            //TODO delete concrete action
        }

        $concrete_action = $this->getConcreteAction($cp_action);
        if (!$concrete_action) {
            throw new Exception("CpGiftActionManager#deletePhysicalRelatedCpActionData cant find concrete action cp_action_id=".$cp_action->id);
        }
        /** @var GiftMessageService $gif_message_service */
        $gif_message_service = $this->getService("GiftMessageService");
        $gif_message_service->deletePhysicalGiftMessageByCpGiftActionId($concrete_action->id);
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        if (!$cp_action || !$cp_user) {
            throw new Exception("CpGiftActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        $concrete_action = $this->getConcreteAction($cp_action);
        if (!$concrete_action) {
            throw new Exception("CpGiftActionManager#deletePhysicalRelatedCpActionData cant find concrete action cp_action_id=".$cp_action->id);
        }
        /** @var GiftMessageService $gif_message_service */
        $gif_message_service = $this->getService("GiftMessageService");
        $gif_message_service->deletePhysicalGiftMessageByCpGiftActionIdAndCpUserId($concrete_action->id, $cp_user->id);
    }
}
