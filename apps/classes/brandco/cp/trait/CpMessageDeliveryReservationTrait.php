<?php

AAFW::import('jp.aainc.classes.entities.CpMessageDeliveryReservation');


trait CpMessageDeliveryReservationTrait {

    protected $reservations;


    public function getReservationStore() {
        return $this->reservations;
    }

    /**
     * @return mixed
     */
    public function getTargetCpMessageDeliveryReservation() {

        $filter = array(
            'conditions' => array(
                "status" => CpMessageDeliveryReservation::STATUS_SCHEDULED,
                "delivery_date:<=" => date('Y/m/d H:i:s'),
                "delivery_type" => array(
                    CpMessageDeliveryReservation::DELIVERY_TYPE_IMMEDIATELY,
                    CpMessageDeliveryReservation::DELIVERY_TYPE_RESERVATION
                )
            ),
            'order' => array(
                'name' => "id"
            )
        );
        return $this->reservations->find($filter);
    }

    public function getTargetCpAnnounceDeliveryReservation() {
        $filter = array(
            'conditions' => array(
                'status' => CpMessageDeliveryReservation::STATUS_SCHEDULED,
                'delivery_date' => CpMessageDeliveryReservation::DELIVERY_DATE_NOT_SEND,
                'delivery_type' => CpMessageDeliveryReservation::DELIVERY_TYPE_NONE
            ),
            'order' => array(
                'name' => 'id'
            )
        );
        return $this->reservations->find($filter);
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCurrentCpMessageDeliveryReservationByCpActionId($cp_action_id) {

        $filter = array(
            'conditions' => array(
                "cp_action_id" => $cp_action_id,
                "status" => array(
                    CpMessageDeliveryReservation::STATUS_DRAFT,
                    CpMessageDeliveryReservation::STATUS_FIX,
                    CpMessageDeliveryReservation::STATUS_SCHEDULED,
                    CpMessageDeliveryReservation::STATUS_DELIVERING,
                    CpMessageDeliveryReservation::STATUS_DELIVERY_FAIL,
                )
            ),
            'order' => array(
                'name' => "id"
            )
        );
        return $this->reservations->findOne($filter);
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpMessageDeliveryReservationsByCpActionId($cp_action_id) {

        $filter = array(
            'conditions' => array(
                "cp_action_id" => $cp_action_id,
            ),
            'order' => array(
                'name' => "id"
            )
        );
        return $this->reservations->find($filter);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getCpMessageDeliveryReservationById($id) {
        return $this->reservations->findOne($id);
    }


    /**
     * @param $cp_action_id
     * @param $type
     * @param $delivery_date
     * @return mixed
     */
    public function createCpMessageDeliveryReservation($cp_action_id, $type, $delivery_date, $delivery_type = CpMessageDeliveryReservation::DELIVERY_TYPE_IMMEDIATELY) {
        $status = CpMessageDeliveryReservation::STATUS_FIX;

        //初めのグループの時は即時でそのまま送れないように下書き状態で作成
        $service_factory = new aafwServiceFactory();
        $cp_flow_service = $service_factory->create('CpFlowService');
        $cp_action = $cp_flow_service->getCpActionById($cp_action_id);
        $cp = $cp_flow_service->getCpByCpAction($cp_action);
        $cpActionGroup = $cp_flow_service->getCpActionGroupByAction($cp_action_id);

        if($cpActionGroup->order_no == 1 && $cp->type == Cp::TYPE_CAMPAIGN){
            $status = CpMessageDeliveryReservation::STATUS_DRAFT;
        }

        $reservation = $this->reservations->createEmptyObject();
        $reservation->cp_action_id = $cp_action_id;
        $reservation->type = $type;
        $reservation->delivery_type = $delivery_type;
        $reservation->delivery_date = $delivery_date;
        $reservation->send_mail_flg = CpMessageDeliveryReservation::SEND_MAIL_ON;
        $reservation->status = $status;
        return $this->reservations->save($reservation);
    }

    /**
     * @param CpMessageDeliveryReservation $reservation
     */
    public function updateCpMessageDeliveryReservation(CpMessageDeliveryReservation $reservation) {
        $this->reservations->save($reservation);
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getDeliveredCpMessageDeliveryReservationByCpActionId($cp_action_id) {

        $filter = array(
            'conditions' => array(
                "cp_action_id" => $cp_action_id,
                "status" => CpMessageDeliveryReservation::STATUS_DELIVERED,
            ),
            'order' => array(
                'name' => "id"
            )
        );
        return $this->reservations->find($filter);
    }

    /**
     * @return mixed
     */
    public function getDeliveredCpMsgDeliveryReservationByMoniplaUpdateStatus() {
        $filter = array(
            'status' => CpMessageDeliveryReservation::STATUS_DELIVERED,
            'monipla_update_status' => CpMessageDeliveryReservation::MONIPLA_STATUS_SCHEDULED,
            'delivery_date:!=' => CpMessageDeliveryReservation::DELIVERY_DATE_NOT_SEND
        );

        return $this->reservations->findOne($filter);
    }
}
