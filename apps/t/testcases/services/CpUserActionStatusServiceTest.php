<?php
AAFW::import ('jp.aainc.classes.services.CpUserActionStatusService');

class CpUserActionStatusServiceTest extends BaseTest {

    public function testCountCpUserActionStatusByCpActionAndStatus01_whenCountZero() {
        $target = aafwServiceFactory::create("CpUserActionStatusService");
        $count = $target->countCpUserActionStatusByCpActionAndStatus(-1);
        $this->assertEquals(0, $count);
    }

    public function testCountCpUserActionStatusByCpActionAndStatus02_whenActionIdIsNull() {
        $target = aafwServiceFactory::create("CpUserActionStatusService");
        $count = $target->countCpUserActionStatusByCpActionAndStatus(null);
        $this->assertNull($count);
    }

    public function testCountCpUserActionStatusByCpActionAndStatus03_whenStatusIsNull() {
        $target = aafwServiceFactory::create("CpUserActionStatusService");
        $count = $target->countCpUserActionStatusByCpActionAndStatus(1, null);
        $this->assertNull($count);
    }

    public function testCountCpUserActionStatusByCpActionAndStatus04_whenCountOne() {
        list($brand1, $cp1, $cp_actio_group1, $cp_action1) = $this->newBrandToAction();
        list($brand2, $cp2, $cp_actio_group2, $cp_action2) = $this->newBrandToAction();

        $user1 = $this->newUser();
        $cp_user1 = $this->entity("CpUsers", array('user_id' => $user1->id, 'cp_id' => $cp1->id));
        $this->entity('CpUserActionStatuses', array('cp_user_id' => $cp_user1->id, 'cp_action_id' => $cp_action1->id));

        $user2 = $this->newUser();
        $cp_user2 = $this->entity("CpUsers", array('user_id' => $user2->id, 'cp_id' => $cp2->id));
        $this->entity('CpUserActionStatuses', array('cp_user_id' => $cp_user2->id, 'cp_action_id' => $cp_action2->id));

        $target = aafwServiceFactory::create("CpUserActionStatusService");
        $count = $target->countCpUserActionStatusByCpActionAndStatus($cp_action1->id);
        $this->assertEquals(1, $count);
    }

    public function testCountCpUserActionStatusByCpActionAndStatus05_whenCountTwo() {
        list($brand1, $cp1, $cp_actio_group1, $cp_action1) = $this->newBrandToAction();
        list($brand2, $cp2, $cp_actio_group2, $cp_action2) = $this->newBrandToAction();

        $user1 = $this->newUser();
        $cp_user1 = $this->entity("CpUsers", array('user_id' => $user1->id, 'cp_id' => $cp1->id));
        $this->entity('CpUserActionStatuses', array('cp_user_id' => $cp_user1->id, 'cp_action_id' => $cp_action1->id));

        $user2 = $this->newUser();
        $cp_user2 = $this->entity("CpUsers", array('user_id' => $user2->id, 'cp_id' => $cp1->id));
        $this->entity('CpUserActionStatuses', array('cp_user_id' => $cp_user2->id, 'cp_action_id' => $cp_action1->id));

        $target = aafwServiceFactory::create("CpUserActionStatusService");
        $count = $target->countCpUserActionStatusByCpActionAndStatus($cp_action1->id);
        $this->assertEquals(2, $count);
    }
}