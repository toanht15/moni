<?php

class CpMessageDeliveryReservationTest extends BaseTest {

    public function test_isFixedAnnounceDeliveryUser() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp_message_delivery_reservation = $this->entity('CpMessageDeliveryReservations', array(
            'cp_action_id' => $cp_action->id,
            'delivery_type' => CpMessageDeliveryReservation::DELIVERY_TYPE_NONE,
            'status' => CpMessageDeliveryReservation::STATUS_DELIVERED,
            'delivery_date' => CpMessageDeliveryReservation::DELIVERY_DATE_NOT_SEND,
        ));

        $this->assertTrue($cp_message_delivery_reservation->isFixedAnnounceDeliveryUser());
    }

    public function test_isFixedAnnounceDeliveryUser_NG() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp_message_delivery_reservation = $this->entity('CpMessageDeliveryReservations', array(
            'cp_action_id' => $cp_action->id,
            'delivery_type' => CpMessageDeliveryReservation::DELIVERY_TYPE_IMMEDIATELY,
            'status' => CpMessageDeliveryReservation::STATUS_DELIVERED,
            'delivery_date' => CpMessageDeliveryReservation::DELIVERY_DATE_NOT_SEND,
        ));

        $this->assertFalse($cp_message_delivery_reservation->isFixedAnnounceDeliveryUser());
    }
}