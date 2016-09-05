<?php

AAFW::import('jp.aainc.classes.batch.UserMessageDeliveryManager');

class UserMessageDeliveryManagerTest extends BaseTest {

    public function setUp() {
        $this->clearBrandAndRelatedEntities();
    }

    public function testEmpty() {
        $this->assertEquals(true, true);
    }

    public function gtestDoProcess01_whenEmptyTargets() {
        $target = new UserMessageDeliveryManager();
        $target->setCore(new UserMessageDeliveryManagerTest_Core());
        $target->setEnableHipChat(false);
        $target->doProcess();
    }

    public function gtestDoProcess02_whenOneTarget() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $cp->type = Cp::TYPE_CAMPAIGN;
        $cp->status = Cp::CAMPAIGN_STATUS_OPEN;
        $cp->public_date = '1970/01/01';
        $cp->end_date = '2100/01/01';
        $this->save('Cps', $cp);
        $user = $this->newUser();
        $this->entity('BrandsUsersRelations', array('user_id' => $user->id, 'brand_id' => $brand->id));
        $rsv = $this->entity("CpMessageDeliveryReservations", array(
            'cp_action_id' => $cp_action->id,
            'status' => CpMessageDeliveryReservation::STATUS_SCHEDULED,
            'delivery_date' => '1970/01/01',
            'delivery_type' => CpMessageDeliveryReservation::DELIVERY_TYPE_IMMEDIATELY
        ));
        $dlv_target = $this->entity("CpMessageDeliveryTargets", array(
                'cp_message_delivery_reservation_id' => $rsv->id,
                'user_id' => $user->id,
                'cp_action_id' => $cp_action->id
            )
        );

        // test
        $target = new UserMessageDeliveryManager();
        $target->setEnableHipChat(false);
        $target->setCore(new UserMessageDeliveryManagerTest_Core());
        $target->doProcess();

        // verify
        $cp_user = $this->findOne('CpUsers', array('user_id' => $user->id));
        $expected_cp_user = $this->emptyEntity("CpUsers", array(
            'cp_id' => $cp->id,
            'user_id' => $user->id,
            'from_id' => '',
            'referrer' => '',
            'beginner_flg' => '0',
            'demography_flg' => '0',
            'join_sns' => '0',
            'del_flg' => '0'
        ));
        $expected_msg = $this->emptyEntity('CpUserActionMessages', array(
            'cp_user_id' => $cp_user->id,
            'cp_action_id' => $cp_action->id,
            'title' => '',
            'read_flg' => '0',
            'del_flg' => '0'
        ));
        $expected_status = $this->emptyEntity('CpUserActionStatuses', array(
            'cp_user_id' => $cp_user->id,
            'cp_action_id' => $cp_action->id,
            'status' => '0',
            'user_agent' => '',
            'device_type' => '0',
            'del_flg' => '0'
        ));
        $expected_mail_queue = $this->emptyEntity('MailQueues', array(
            'send_schedule' => '1970-01-01 00:00:00',
            'to_address' => 'hogepiyofx@gmail.com',
            'cc_address' => '',
            'bcc_address' => '',
            'subject' => '',
            'from_address' => "<dummy@aainc.co.jp>",
            'envelope' => 'dummy@aainc.co.jp',
            'user_id' => $user->id,
            'cp_message_delivery_reservation_id' => $rsv->id,
            'del_flg' => '0'
        ));
        $expected = array(
            'reservation_status' => CpMessageDeliveryReservation::STATUS_DELIVERED,
            'target_status' => CpMessageDeliveryTarget::STATUS_DELIVERED,
            'cp_user' => $this->convertToJson($expected_cp_user),
            'message' => $this->convertToJson($expected_msg),
            'status' => $this->convertToJson($expected_status),
            'mail_queue' => $this->convertToJson($expected_mail_queue)
        );

        $this->assertEquals(
            $expected,
            array(
                'reservation_status' => $this->findOne('CpMessageDeliveryReservations', array('id' => $rsv->id))->status,
                'target_status' => $this->findOne('CpMessageDeliveryTargets', array('id' => $dlv_target->id))->status,
                'cp_user' => $this->findOneAsJson('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id)),
                'message' => $this->findOneAsJson('CpUserActionMessages', array('cp_user_id' => $cp_user->id)),
                'status' => $this->findOneAsJson('CpUserActionStatuses', array('cp_user_id' => $cp_user->id)),
                'mail_queue' => $this->findOneAsJson('MailQueues', array('user_id' => $user->id), array('body_plain', 'body_html'))
            )
        );
    }

    public function gtestDoProcess03_whenNotScheduledReservation() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $cp->type = Cp::TYPE_CAMPAIGN;
        $cp->status = Cp::STATUS_SCHEDULE;
        $cp->public_date = '2100/01/01';
        $cp->end_date = '2100/01/01';
        $this->save('Cps', $cp);
        $user = $this->newUser();
        $this->entity('BrandsUsersRelations', array('user_id' => $user->id, 'brand_id' => $brand->id));
        $rsv = $this->entity("CpMessageDeliveryReservations", array(
            'cp_action_id' => $cp_action->id,
            'status' => CpMessageDeliveryReservation::STATUS_SCHEDULED,
            'delivery_date' => '1970/01/01',
            'delivery_type' => CpMessageDeliveryReservation::DELIVERY_TYPE_IMMEDIATELY
        ));

        $target = new UserMessageDeliveryManager();
        $target->setEnableHipChat(false);
        $target->doProcess();
    }

}

class UserMessageDeliveryManagerTest_Core {

    public function getUserByQuery($arg) {
        $result = new UserMessageDeliveryManagerTest_Result();
        $result->status = Thrift_APIStatus::SUCCESS;
        $user = new UserMessageDeliveryManagerTest_User();
        $user->result = $result;
        $user->mailAddress = "hogepiyofx@gmail.com";

        return $user;
    }
}

class UserMessageDeliveryManagerTest_User {

    public $result;

    public $mailAddress;
}

class UserMessageDeliveryManagerTest_Result {

    public $status;
}