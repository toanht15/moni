<?php

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;

/**
 * Class CpCouponActionManager
 */
class CpCouponActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    /** @var aafwEntityStoreBase $cp_coupon_actions */
    protected $cp_coupon_actions;
    /** @var aafwEntityStoreBase $cp_coupon_actions */
    protected $coupon_code_users;
    /** @var aafwEntityStoreBase $coupon_codes */
    protected $coupon_codes;
    /** @var aafwEntityStoreBase $coupons */
    protected $coupons;

    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_coupon_actions = $this->getModel("CpCouponActions");
        $this->coupon_code_users = $this->getModel("CouponCodeUsers");
        $this->coupon_codes = $this->getModel('CouponCodes');
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
            $entry_action = $this->getCpCouponActionByCpAction($cp_action);
        }
        return array($cp_action, $entry_action);

    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCouponIdByCpActionId($cp_action_id) {
        $action = $this->getCpActions($cp_action_id);
        return $action[1]->coupon_id;
    }

    /**
     * @param $coupon_code_id
     * @return aafwEntityContainer|array
     */
    public function getReservedCouponCodeUserByCouponCodeId($coupon_code_id) {
        return $this->coupon_code_users->find(array('coupon_code_id' => $coupon_code_id));
    }

    /**
     * @param $action_id
     * @return aafwEntityContainer|array
     */
    public function getReservedCouponCodeUserByActionId($action_id) {
        return $this->coupon_code_users->find(array('cp_action_id' => $action_id));
    }

    /**
     * @param $user_id
     * @param $action_id
     * @return entity
     */
    public function getReservedCouponCodeUserByUserIdAndActionId($user_id, $action_id) {
        return $this->coupon_code_users->findOne(array('user_id' => $user_id, 'cp_action_id' => $action_id));
    }

    /**
     * @param $coupon_id
     * @return aafwEntityContainer|array
     */
    public function getCpCouponActionsByCouponId($coupon_id) {
        return $this->cp_coupon_actions->find(array('coupon_id' => $coupon_id));
    }

    /**
     * @param $coupon_code_id
     * @return int
     */
    public function getCouponCodeReservedNum($coupon_code_id) {
        $coupon_code_users = $this->getReservedCouponCodeUserByCouponCodeId($coupon_code_id);
        return $coupon_code_users ? $coupon_code_users->total() : 0;
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
     * @param $code_id
     * @param $user_id
     * @param $cp_action_id
     * @return mixed
     */
    public function createCouponCodeUser($code_id, $user_id, $cp_action_id) {
        $coupon_code_user = $this->coupon_code_users->createEmptyObject();
        $coupon_code_user->coupon_code_id = $code_id;
        $coupon_code_user->user_id = $user_id;
        $coupon_code_user->cp_action_id = $cp_action_id;
        $this->coupon_code_users->save($coupon_code_user);

        // increment count
        $coupon_code = $this->coupon_codes->findOne(array("id" => $code_id, "for_update" => true));
        if (!$coupon_code) {
            return;
        }
        $coupon_code->reserved_num ++;
        $this->coupon_codes->save($coupon_code);
    }

    public function restoreCouponCode($code_id) {
        if (!$code_id) {
            return;
        }

        $coupon_code = $this->coupon_codes->findOne(array("id" => $code_id, "for_update" => true));
        if (!$coupon_code) {
            return;
        }
        $coupon_code->reserved_num --;
        $this->coupon_codes->save($coupon_code);

        //coupon->reserved_numを使わないので操作しない
//        $coupon_store = $this->getCouponStore();
//        $coupon = $coupon_store->findOne(array("id" => $coupon_code->coupon_id, "for_update" => true));
//        $coupon->reserved_num --;
//        $coupon_store->save($coupon);
    }

    public function getCouponStore() {
        if (!$this->coupons) {
            $this->coupons = $this->getModel("Coupons");
        }
        return $this->coupons;
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
        return $this->getCpCouponActionByCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $action = $this->cp_coupon_actions->createEmptyObject();
        $action->cp_action_id = $cp_action->id;
        $action->title = 'クーポン';
        $action->text = "";
        $this->cp_coupon_actions->save($action);
        return $action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $action = $this->getCpCouponActionByCpAction($cp_action);

        try {
            if ($action->coupon_id != $data["coupon_id"]) {
                $coupon_store = $this->getModel('Coupons');
                $cp = $cp_action->getCp();
                $this->cp_coupon_actions->begin();
                $cp_action_group = $cp_action->getCpActionGroup();
                if ($cp_action_group->order_no == 1) {
                    if ($action->coupon_id) {
                        $coupon = $coupon_store->findOne($action->coupon_id);
                        $coupon->reserved_num = $coupon->countReservedNum() - $cp->winner_count;
                        $coupon_store->save($coupon);
                    }

                    $coupon = $coupon_store->findOne($data["coupon_id"]);
                    if ($coupon) {
                        $coupon->reserved_num = $coupon->countReservedNum() + $cp->winner_count;
                        $coupon_store->save($coupon);
                    }
                }
            }
            $action->image_url = $data["image_url"];
            $action->text = $data["text"];
            $action->html_content = Markdown::defaultTransform($data["text"]);
            $action->title = $data["title"];
            $action->coupon_id = $data["coupon_id"];
            $action->del_flg = 0;
            $this->cp_coupon_actions->save($action);

            $this->cp_coupon_actions->commit();
        } catch (Exception $e) {
            $this->cp_coupon_actions->rollback();
            $this->logger->error($e);
        }
    }

    /**
     * @param $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $action = $this->getCpCouponActionByCpAction($cp_action);
        $action->del_flg = 1;
        $this->cp_coupon_actions->save($action);
    }

    /**
     * @param $cp_action
     * @return mixed
     */
    private function getCpCouponActionByCpAction(CpAction $cp_action) {
        return $this->cp_coupon_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_coupon_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->image_url = $old_concrete_action->image_url;
        $new_concrete_action->text = $old_concrete_action->text;
        $new_concrete_action->title = $old_concrete_action->title;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        if (!$new_concrete_action->text) {
            $new_concrete_action->text = "";
        }
        return $this->cp_coupon_actions->save($new_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {

        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpCouponActionManager#deletePhysicalRelatedCpActionData cp_action_id=".$cp_action->id);
        }

        if ($with_concrete_actions) {
            //TODO delete concrete action
        }

        //delete coupon_code_user
        $coupon_code_users = $this->coupon_code_users->find(array('cp_action_id' => $cp_action->id));
        if (!$coupon_code_users) {
            return;
        }
        foreach ($coupon_code_users as $coupon_code_user) {
            $this->coupon_code_users->deletePhysical($coupon_code_user);
            $this->restoreCouponCode($coupon_code_user->coupon_code_id);
        }
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {

        if (!$cp_action || !$cp_user) {
            throw new Exception("CpCouponActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=".$cp_action->id);
        }

        $coupon_code_users = $this->coupon_code_users->find(array('cp_action_id' => $cp_action->id, "user_id" => $cp_user->user_id));
        if (!$coupon_code_users) {
            return;
        }
        foreach ($coupon_code_users as $coupon_code_user) {
            $this->coupon_code_users->deletePhysical($coupon_code_user);
            $this->restoreCouponCode($coupon_code_user->coupon_code_id);
        }
    }

    /**
     * @param $user_id
     * @param $cp_action_id
     * @return mixed
     */
    public function getCouponCodeUser($user_id, $cp_action_id) {
        $filter = array(
            'conditions' => array(
                'user_id'   => $user_id,
                'cp_action_id' => $cp_action_id,
            )
        );
        return $this->coupon_code_users->findOne($filter);
    }
}
