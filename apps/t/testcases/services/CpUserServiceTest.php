<?php
AAFW::import ('jp.aainc.classes.services.CpUserService');

class CpUserServiceTest extends BaseTest {

    /** @var  CpUserService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("CpUserService");
    }

    public function testGetCpUserById_whenIdExists() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $user = $this->entity('Users', array('monipla_user_id' => $this->max('Users', 'monipla_user_id') + 1));
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));

        $result = $this->target->getCpUserById($cp_user->id);

        $this->assertEquals(
            array('id' => $cp_user->id, 'cp_id' => $cp_user->cp_id, 'user_id' => $cp_user->user_id),
            array('id' => $result->id, 'cp_id' => $result->cp_id, 'user_id' => $result->user_id));
    }

    public function testGetCpUserById_whenIdNotExists() {
        $result = $this->target->getCpUserById(-100);
        $this->assertNull($result);
    }

    public function testGetCpUserByCpIdAndUserId_whenMatched() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $user = $this->entity('Users', array('monipla_user_id' => $this->max('Users', 'monipla_user_id') + 1));
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));

        $result = $this->target->getCpUserByCpIdAndUserId($cp_user->cp_id, $cp_user->user_id);

        $this->assertEquals(
            array('id' => $cp_user->id, 'cp_id' => $cp_user->cp_id, 'user_id' => $cp_user->user_id),
            array('id' => $result->id, 'cp_id' => $result->cp_id, 'user_id' => $result->user_id));
    }

    public function testGetCpUserByCpIdAndUserId_whenNotMatched() {
        $result = $this->target->getCpUserByCpIdAndUserId(-100, -100);
        $this->assertNull($result);
    }

    public function testGetCpUserActionStatus_whenMatched() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id));
        $user = $this->entity('Users', array('monipla_user_id' => $this->max('Users', 'monipla_user_id') + 1));
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $status = $this->entity('CpUserActionStatuses', array('cp_action_id' => $cp_action->id, 'cp_user_id' => $cp_user->id));

        $result = $this->target->getCpUserActionStatus($cp_user->id, $cp_action->id);

        $this->assertEquals(
            array('id' => $status->id, 'cp_action_id' => $status->cp_action_id, 'cp_user_id' => $status->cp_user_id),
            array('id' => $result->id, 'cp_action_id' => $result->cp_action_id, 'cp_user_id' => $result->cp_user_id)
        );
    }
    public function testGetCpUserActionStatus_whenMatchedOnMaster() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id));
        $user = $this->entity('Users', array('monipla_user_id' => $this->max('Users', 'monipla_user_id') + 1));
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $status = $this->entity('CpUserActionStatuses', array('cp_action_id' => $cp_action->id, 'cp_user_id' => $cp_user->id));

        $result = $this->target->getCpUserActionStatus($cp_user->id, $cp_action->id, true);

        $this->assertEquals(
            array('id' => $status->id, 'cp_action_id' => $status->cp_action_id, 'cp_user_id' => $status->cp_user_id),
            array('id' => $result->id, 'cp_action_id' => $result->cp_action_id, 'cp_user_id' => $result->cp_user_id)
        );
    }
    public function testGetCpUserActionStatus_whenNotMatched() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id));
        $user = $this->entity('Users', array('monipla_user_id' => $this->max('Users', 'monipla_user_id') + 1));
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $this->entity('CpUserActionStatuses', array('cp_action_id' => $cp_action->id, 'cp_user_id' => $cp_user->id));

        $result = $this->target->getCpUserActionStatus(-100, -100);

        $this->assertNull($result);
    }

    public function testGetAllCpUserActionMessagesByCpUserIdOrderByActionOrder_whenAsc() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $user = $this->entity('Users', array('monipla_user_id' => $this->max('Users', 'monipla_user_id') + 1));
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));

        $cp_action1 = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'order_no' => 1));
        $cp_action2 = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'order_no' => 2));

        $message1 = $this->entity('CpUserActionMessages', array('cp_action_id' => $cp_action1->id, 'cp_user_id' => $cp_user->id));
        $message2 = $this->entity('CpUserActionMessages', array('cp_action_id' => $cp_action2->id, 'cp_user_id' => $cp_user->id));

        $asc = true;
        $messages = $this->target->getAllCpUserActionMessagesByCpUserIdOrderByActionOrder($cp_user->id, $asc)->toArray();

        $this->assertEquals($message1->id, $messages[0]->id);
    }

    public function testGetAllCpUserActionMessagesByCpUserIdOrderByActionOrder_whenDesc() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $user = $this->entity('Users', array('monipla_user_id' => $this->max('Users', 'monipla_user_id') + 1));
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));

        $cp_action1 = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'order_no' => 1));
        $cp_action2 = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'order_no' => 2));

        $message1 = $this->entity('CpUserActionMessages', array('cp_action_id' => $cp_action1->id, 'cp_user_id' => $cp_user->id));
        $message2 = $this->entity('CpUserActionMessages', array('cp_action_id' => $cp_action2->id, 'cp_user_id' => $cp_user->id));

        $asc = false;
        $messages = $this->target->getAllCpUserActionMessagesByCpUserIdOrderByActionOrder($cp_user->id, $asc)->toArray();

        $this->assertEquals($message2->id, $messages[0]->id);
    }
}