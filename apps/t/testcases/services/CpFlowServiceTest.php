<?php
AAFW::import ('jp.aainc.classes.services.CpFlowService');

class CpFlowServiceTest extends BaseTest {

    /** @var  CpFlowService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("CpFlowService");
    }

    public function testGetCampaignInfo01() {
        $brand = $this->entity('Brands', array("directory_name" => "new_brand"));
        $cp = $this->entity("Cps", array("brand_id" => $brand->id));
        $cp_action_group = $this->entity("CpActionGroups", array("cp_id" => $cp->id, "order_no" => 1));
        $cp_action = $this->entity("CpActions", array("title" => "TEST", "type" => CpAction::TYPE_MESSAGE, "cp_action_group_id" => $cp_action_group->id, "order_no" => 1));
        $this->entity("CpMessageActions", array("cp_action_id" => $cp_action->id));

        $result = $this->target->getCampaignInfo($cp, $brand);

        $expected = array(
            "cp" => array(
                "id" => $cp->id,
                "created_at" => null,
                "can_entry" => false,
                "sponsor" => null,
                "url" => "https://brandcotest.com/new_brand/campaigns/{$cp->id}",
                "shipping_method" => null,
                "winner_count" => null,
                "show_winner_label" => null,
                "winner_label" => null,
                "show_recruitment_note" => null,
                "recruitment_note" => null,
                "back_monipla_flg" => null,
                "extend_tag" => "",
                "start_date" => "1970/01/01（木）",
                "start_datetime" => "1970/01/01 (木) 09:00",
                "end_date" => "1970/01/01（木）",
                "end_datetime" => "1970/01/01 (木) 09:00",
                "announce_date" => "1970/01/01（木）",
                'status' => null,
                'title' => '名称未設定のキャンペーン',
                "announce_display_label_use_flg" => $cp->announce_display_label_use_flg,
                "announce_display_label" => $cp->announce_display_label
            ),
            "tweet_share_text" => "名称未設定のキャンペーン / "
        );
        $this->assertEquals($expected, $result);
    }

    public function testCheckCpActionTypesInCp01_oneType() {
        $brand = $this->entity('Brands');

        $cp1 = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp2 = $this->entity('Cps', array('brand_id' => $brand->id));

        $cp_action_group1 = $this->entity('CpActionGroups', array('cp_id' => $cp1->id));
        $cp_action_group2 = $this->entity('CpActionGroups', array('cp_id' => $cp2->id));

        $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group1->id, 'type' => CpAction::TYPE_ENTRY));
        $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group2->id, 'type' => CpAction::TYPE_ENTRY));

        $cp_action_type_set = $this->target->checkCpActionTypesInCp($cp1->id, array(CpAction::TYPE_ENTRY));

        $this->assertEquals(array(CpAction::TYPE_ENTRY => true), $cp_action_type_set);
    }

    public function testCheckCpActionTypesInCp02_twoType() {
        $brand = $this->entity('Brands');

        $cp1 = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp2 = $this->entity('Cps', array('brand_id' => $brand->id));

        $cp_action_group1 = $this->entity('CpActionGroups', array('cp_id' => $cp1->id));
        $cp_action_group2 = $this->entity('CpActionGroups', array('cp_id' => $cp2->id));

        $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group1->id, 'type' => CpAction::TYPE_ENTRY));
        $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group1->id, 'type' => CpAction::TYPE_ENGAGEMENT));
        $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group1->id, 'type' => CpAction::TYPE_FREE_ANSWER));
        $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group2->id, 'type' => CpAction::TYPE_ENTRY));
        $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group2->id, 'type' => CpAction::TYPE_FREE_ANSWER));

        $cp_action_type_set = $this->target->checkCpActionTypesInCp($cp1->id, array(CpAction::TYPE_ENTRY, CpAction::TYPE_FREE_ANSWER));

        $this->assertEquals(array(CpAction::TYPE_ENTRY => true, CpAction::TYPE_FREE_ANSWER => true), $cp_action_type_set);
    }

    public function testCheckCpActionTypesInCp03_CpIdIsNull() {
        $cp_action_type_set = $this->target->checkCpActionTypesInCp(null, array(CpAction::TYPE_ENTRY, CpAction::TYPE_FREE_ANSWER));
        $this->assertEquals(array(), $cp_action_type_set);
    }

    public function testCheckCpActionTypesInCp04_TypeArrayIsNull() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_type_set = $this->target->checkCpActionTypesInCp($cp->id, array(CpAction::TYPE_ENTRY, CpAction::TYPE_FREE_ANSWER));
        $this->assertEquals(array(), $cp_action_type_set);
    }

    public function testDeleteCpNextActionsInGroup() {
        $brand = $this->entity('Brands');

        $cp1 = $this->entity('Cps', array('brand_id' => $brand->id));

        $cp_action_group1 = $this->entity('CpActionGroups', array('cp_id' => $cp1->id, 'order_no' => 1));
        $cp_action_group2 = $this->entity('CpActionGroups', array('cp_id' => $cp1->id, 'order_no' => 2));

        $cp_actions = $this->entities('CpActions', array(
            array('cp_action_group_id' => $cp_action_group1->id, 'type' => CpAction::TYPE_ENTRY, 'order_no' => 1),
            array('cp_action_group_id' => $cp_action_group1->id, 'type' => CpAction::TYPE_ENGAGEMENT, 'order_no' => 2),
            array('cp_action_group_id' => $cp_action_group1->id, 'type' => CpAction::TYPE_FREE_ANSWER, 'order_no' => 3),
            array('cp_action_group_id' => $cp_action_group2->id, 'type' => CpAction::TYPE_ENTRY, 'order_no' => 1),
            array('cp_action_group_id' => $cp_action_group2->id, 'type' => CpAction::TYPE_FREE_ANSWER, 'order_no' => 2)
            )
        );

        $this->entities('CpNextActions',  array(
            array('cp_action_id' => $cp_actions[0]->id, 'cp_next_action_id' => $cp_actions[1]->id),
            array('cp_action_id' => $cp_actions[1]->id, 'cp_next_action_id' => $cp_actions[2]->id),
            array('cp_action_id' => $cp_actions[2]->id, 'cp_next_action_id' => $cp_actions[3]->id),
            array('cp_action_id' => $cp_actions[3]->id, 'cp_next_action_id' => $cp_actions[4]->id),
            )
        );
        $this->target->deleteCpNextActionsInGroup($cp_actions);

        $cp_next_action = array();
        foreach($cp_actions as $cp_action) {
            if(!$next_action = $this->target->getCpNextActionByCpActionId($cp_action->id)) {
                continue;
            }
            $cp_next_action[] = $next_action;
        }

        $this->assertEmpty($cp_next_action);
    }

    public function testCanShiftAction_ActionTypeEntry() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ENTRY));

        $this->assertNotTrue($this->target->canShiftAction($cp, $cp_action_group, $action));
    }

    public function testCanShiftAction_CpTypeNonincentive() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => Cp::TYPE_CAMPAIGN));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 1));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_QUESTIONNAIRE, 'order_no' => 1));

        $this->assertNotTrue($this->target->canShiftAction($cp, $cp_action_group, $action));
    }

    public function testCanShiftAction_ActionTypeMessage() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_MESSAGE));

        $this->assertTrue($this->target->canShiftAction($cp, $cp_action_group, $action));
    }

    public function testCanSortAction_ActionTypeEntry() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ENTRY));

        $this->assertNotTrue($this->target->canSortAction($cp, $cp_action_group, $action));
    }

    public function testCanSortAction_ActionTypeFinish() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_JOIN_FINISH));

        $this->assertNotTrue($this->target->canSortAction($cp, $cp_action_group, $action));
    }

    public function testCanSortAction_ActionTypeInstantwin() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_INSTANT_WIN));

        $this->assertNotTrue($this->target->canSortAction($cp, $cp_action_group, $action));
    }

    public function testCanSortAction_CpTypeNoninsentive() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => Cp::TYPE_CAMPAIGN));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 1));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_QUESTIONNAIRE, 'order_no' => 1));

        $this->assertNotTrue($this->target->canSortAction($cp, $cp_action_group, $action));
    }

    public function testCanSortAction_CpTypeAnnounceSelectionGroup1Total1() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => Cp::TYPE_CAMPAIGN, 'selection_method'=>CpCreator::ANNOUNCE_SELECTION));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 1));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ANNOUNCE, 'order_no' => 1));

        $this->assertTrue($this->target->canSortAction($cp, $cp_action_group, $action));
    }

    public function testCanSortAction_CpTypeAnnounceSelectionGroup2Total1() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => Cp::TYPE_CAMPAIGN, 'selection_method'=>CpCreator::ANNOUNCE_SELECTION));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 2));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ANNOUNCE, 'order_no' => 1));

        $this->assertNotTrue($this->target->canSortAction($cp, $cp_action_group, $action));
    }

    public function testCanSortAction_CpTypeAnnounceSelectionGroup2Total2() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => Cp::TYPE_CAMPAIGN, 'selection_method'=>CpCreator::ANNOUNCE_SELECTION));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 2));
        $action1 = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ANNOUNCE, 'order_no' => 1));
        $action2 = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ANNOUNCE, 'order_no' => 2));

        $this->assertTrue($this->target->canSortAction($cp, $cp_action_group, $action1));
    }

    public function testCanSortAction_CpTypeAnnounceFirstGroup2Total1() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => Cp::TYPE_CAMPAIGN, 'selection_method'=>CpCreator::ANNOUNCE_FIRST));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 2));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ANNOUNCE, 'order_no' => 1));

        $this->assertTrue($this->target->canSortAction($cp, $cp_action_group, $action));
    }

    public function testCanSortAction_CpTypeAnnounceFirstGroup1Total1() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => Cp::TYPE_CAMPAIGN, 'selection_method'=>CpCreator::ANNOUNCE_LOTTERY));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 1));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ANNOUNCE, 'order_no' => 1));

        $this->assertNotTrue($this->target->canSortAction($cp, $cp_action_group, $action));
    }

    public function testCanSortAction_CpTypeAnnounceFirstGroup1Total2() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => Cp::TYPE_CAMPAIGN, 'selection_method'=>CpCreator::ANNOUNCE_LOTTERY));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 1));
        $action1 = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ANNOUNCE, 'order_no' => 1));
        $action2 = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ANNOUNCE, 'order_no' => 2));

        $this->assertTrue($this->target->canSortAction($cp, $cp_action_group, $action1));
    }

    public function testgetNotEditableGroups_notCpId() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => Cp::TYPE_CAMPAIGN, 'selection_method'=>CpCreator::ANNOUNCE_LOTTERY));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 1));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ANNOUNCE, 'order_no' => 1));

        $editable_groups = $this->target->getNotEditableGroups(null);
        $this->assertNotTrue($editable_groups);
    }

    public function testgetNotEditableGroups_editable() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => Cp::TYPE_CAMPAIGN, 'selection_method'=>CpCreator::ANNOUNCE_LOTTERY));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 1));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ANNOUNCE, 'order_no' => 1));

        $editable_groups = $this->target->getNotEditableGroups($cp->id);
        $this->assertEmpty($editable_groups);
    }

    public function testgetNotEditableGroups_existReservation() {
        $brand = $this->entity('Brands');

        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => Cp::TYPE_CAMPAIGN, 'selection_method'=>CpCreator::ANNOUNCE_LOTTERY));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 1));
        $action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => CpAction::TYPE_ANNOUNCE, 'order_no' => 1));
        $this->entity('CpMessageDeliveryReservations', array('cp_action_id' => $action->id, 'status' => CpMessageDeliveryReservation::STATUS_SCHEDULED));
        $editable_groups = $this->target->getNotEditableGroups($cp->id);

        $this->assertEquals($cp_action_group->id, $editable_groups[0]['group_id']);
    }

    public function testgetNotEditableGroups_existActionMessages() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $this->entity('CpUserActionMessages', array('cp_action_id' => $cp_action->id, 'cp_user_id' => $cp_user->id));
        $editable_groups = $this->target->getNotEditableGroups($cp->id);

        $this->assertEquals($cp_action_group->id, $editable_groups[0]['group_id']);
    }

}