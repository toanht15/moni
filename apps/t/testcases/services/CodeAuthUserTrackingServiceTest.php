<?php

class CodeAuthUserTrackingServiceTest extends BaseTest {

    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("CodeAuthUserTrackingService");
    }

    public function testTrackingUserWhenEmpty (){
        $user = $this->newUser();
        $brandToAction = $this->newBrandToAction();
        $cp_action_id = $brandToAction[3]->id;
        $this->target->trackingUser($user->id,$cp_action_id);
        $result = $this->findOne('CodeAuthUserTrackingLogs', array('cp_action_id' => $cp_action_id));
        $this->assertEquals($cp_action_id , $result->cp_action_id);
    }



    public function testTrackingUserWhenExist (){
        $user = $this->newUser();
        $brandToAction = $this->newBrandToAction();
        $cp_action_id = $brandToAction[3]->id;
        $this->target->trackingUser($user->id,$cp_action_id);
        $result = $this->findOne('CodeAuthUserTrackingLogs', array('cp_action_id' => $cp_action_id));
        $this->target->trackingUser($result ->user_id,$result->cp_action_id);
        $check_duplicate = $this->find('CodeAuthUserTrackingLogs', array('cp_action_id' => $result->cp_action_id));
        $this->assertEquals(1 ,count($check_duplicate));
    }

    public function testUntrackUserWhenFalse() {
        $user = $this->newUser();
        $brandToAction = $this->newBrandToAction();
        $cp_action_id = $brandToAction[3]->id;
        $check = $this->target->untrackUser($user->id,$cp_action_id);
        $this->assertEquals(false ,$check);
    }

    public function testUntrackUserWhenTrue() {
        $user = $this->newUser();
        $brandToAction = $this->newBrandToAction();
        $cp_action_id = $brandToAction[3]->id;
        $this->target->trackingUser($user->id,$cp_action_id);
        $result = $this->findOne('CodeAuthUserTrackingLogs', array('cp_action_id' => $cp_action_id));
        $this->target->untrackUser($result->user_id,$result->cp_action_id);
        $check = $this->findOne('CodeAuthUserTrackingLogs', array('cp_action_id' => $cp_action_id));
        $this->assertEquals("0000-00-00 00:00:00" ,$check->acc_locking_expire_date);
    }

    public function testTrackinPhysicalLogDelete() {
        $user = $this->newUser();
        $brandToAction = $this->newBrandToAction();
        $cp_action_id = $brandToAction[3]->id;
        $this->target->trackingUser($user->id,$cp_action_id);
        $result = $this->findOne('CodeAuthUserTrackingLogs', array('cp_action_id' => $cp_action_id));
        $this->target->deletePhysicalTrackingLogByCpActionId($result->cp_action_id);
        $check = $this->findOne('CodeAuthUserTrackingLogs', array('cp_action_id' => $cp_action_id));
        $this->assertEquals(null,$check);
    }

    public function testTrackinPhysicalLogDeleteByCpActionIdAndUserId() {
        $user = $this->newUser();
        $brandToAction = $this->newBrandToAction();
        $cp_action_id = $brandToAction[3]->id;
        $this->target->trackingUser($user->id,$cp_action_id);
        $result = $this->findOne('CodeAuthUserTrackingLogs', array('cp_action_id' => $cp_action_id));
        $this->target->deletePhysicalTrackingLogByCpActionIdAndUserId($result->cp_action_id,$result->user_id);
        $check = $this->findOne('CodeAuthUserTrackingLogs', array('cp_action_id' => $cp_action_id));
        $this->assertEquals(null,$check);
    }




}