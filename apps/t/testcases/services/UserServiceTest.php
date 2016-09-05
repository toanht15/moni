<?php
AAFW::import ('jp.aainc.classes.services.UserService');

class UserServiceTest extends BaseTest {

    /** @var  UserService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("UserService");
    }

    public function testCountUnreadMessages01() {
        $brand1 = $this->entity('Brands');
        $brand2 = $this->entity('Brands');

        $cp1 = $this->entity('Cps', array('brand_id' => $brand1->id, 'status' => Cp::STATUS_FIX));
        $cp2 = $this->entity('Cps', array('brand_id' => $brand2->id, 'status' => Cp::STATUS_FIX));

        $cp_action_group1 = $this->entity('CpActionGroups', array('cp_id' => $cp1->id));
        $cp_action_group2 = $this->entity('CpActionGroups', array('cp_id' => $cp2->id));

        $cp_action1 = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group1->id));
        $cp_action2 = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group2->id));
        $cp_action3 = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group1->id));

        $user1 = $this->newUser();
        $user2 = $this->newUser();

        $cp_user1 = $this->entity('CpUsers', array('user_id' => $user1->id, 'cp_id' => $cp1->id));
        $cp_user2 = $this->entity('CpUsers', array('user_id' => $user1->id, 'cp_id' => $cp2->id));
        $this->entity('CpUsers', array('user_id' => $user2->id, 'cp_id' => $cp1->id));
        $this->entity('CpUsers', array('user_id' => $user2->id, 'cp_id' => $cp2->id));

        // この2件がunread countの対象
        $this->entity('CpUserActionMessages', array('cp_action_id' => $cp_action1->id, 'cp_user_id' => $cp_user1->id));
        $this->entity('CpUserActionMessages', array('cp_action_id' => $cp_action2->id, 'cp_user_id' => $cp_user1->id));

        $this->entity('CpUserActionMessages', array('cp_action_id' => $cp_action3->id, 'cp_user_id' => $cp_user1->id, 'read_flg' => 1));
        $this->entity('CpUserActionMessages', array('cp_action_id' => $cp_action1->id, 'cp_user_id' => $cp_user2->id));
        $this->entity('CpUserActionMessages', array('cp_action_id' => $cp_action2->id, 'cp_user_id' => $cp_user2->id));

        $unread_count = $this->target->countUnreadMessages($brand1->id, $user1->id);

        $expected_unread_count = 2;
        $this->assertEquals($expected_unread_count, $unread_count);
    }
}