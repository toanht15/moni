<?php

AAFW::import('jp.aainc.classes.entities.CpMessageDeliveryTarget');


trait CpMessageDeliveryTargetTrait {

    protected $targets;
    protected $users;

    /**
     * @param $reservation_id
     * @return mixed
     */

    public function getCpMessageDeliveryTargetsByReservationId($reservation_id) {

        $filter = array(
            'conditions' => array(
                "cp_message_delivery_reservation_id" => $reservation_id,
                "status" => CpMessageDeliveryTarget::STATUS_RESERVED
            ),
            'order' => array(
                'name' => "id"
            )
        );
        return $this->targets->find($filter);
    }

    /**
     * @param $reservation_id
     * @return mixed
     */
    public function getTargetsByReservationId($reservation_id) {
        $filter = array(
            'cp_message_delivery_reservation_id' => $reservation_id
        );

        return $this->targets->find($filter);
    }

    /**
     * @param $reservation_id
     * @return mixed
     */
    public function getFixedTargetByReservationId($reservation_id) {
        $filter = array(
            "cp_message_delivery_reservation_id" => $reservation_id,
            "fix_target_flg" => CpMessageDeliveryTarget::FIX_TARGET_ON
        );

        return $this->targets->findOne($filter);
    }

    /**
     * @param $reservation_id
     * @param $user_ids
     * @return mixed
     */
    public function getCpMessageDeliveryTargetsByReservationIdAndUserIds($reservation_id, $user_ids) {

        $filter = array(
            'conditions' => array(
                "cp_message_delivery_reservation_id" => $reservation_id,
                "user_id" => $user_ids,
            ),
            'order' => array(
                'name' => "id"
            )
        );
        return $this->targets->find($filter);
    }

    /**
     * @param $reservation_id
     * @param $cp_action_id
     * @param $user_id
     * @return mixed
     */
    public function createCpMessageDeliveryTarget($reservation_id, $cp_action_id, $user_id) {
        $target = $this->targets->createEmptyObject();
        $target->cp_message_delivery_reservation_id = $reservation_id;
        $target->user_id = $user_id;
        $target->cp_action_id = $cp_action_id;
        $target->status = CpMessageDeliveryTarget::STATUS_RESERVED;
        return $this->targets->save($target);
    }

    /**
     * @param $reservation_id
     * @return mixed
     */
    public function getCpMessageDeliveryTargetsCountByReservationId($reservation_id) {

        $filter = array(
            'conditions' => array(
                "cp_message_delivery_reservation_id" => $reservation_id,
            ),
        );
        return $this->targets->count($filter);
    }

    /**
     * @param $action_id
     * @param $user_id
     * @return mixed
     */
    public function getDeliveredTargetCountByActionId($action_id) {
        $filter = array(
            'conditions' => array(
                "cp_action_id" => $action_id,
                "status" => CpMessageDeliveryTarget::STATUS_DELIVERED
            ),
        );
        return $this->targets->count($filter);
    }

    /**
     * @param $action_id
     * @param $user_id
     * @return mixed
     */
    public function getCurrentMessageReservedTarget($reservation_id, $user_ids) {
        $filter = array(
            'conditions' => array(
                "cp_message_delivery_reservation_id" => $reservation_id,
                "status" => CpMessageDeliveryTarget::STATUS_RESERVED
            ),
        );

        if(!Util::isNullOrEmpty($user_ids)){
            $filter['conditions']['user_id'] = $user_ids;
        }

        return $this->targets->find($filter);
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getDeliveredTargetsByCpActionId ($cp_action_id){
        $filter = array(
            'conditions' => array(
                "cp_action_id" => $cp_action_id,
                "status" => CpMessageDeliveryTarget::STATUS_DELIVERED
            ),
            'order' => array(
                'name' => "id"
            )
        );

        return $this->targets->find($filter);
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function getDeliveredCpMessageDeliveryTargetsByUserId ($user_id){
        $filter = array(
            'conditions' => array(
                "user_id" => $user_id,
                "status"  => CpMessageDeliveryTarget::STATUS_DELIVERED
            ),
            'order' => array(
                'name' => "id"
            )
        );

        return $this->targets->find($filter);
    }

}
