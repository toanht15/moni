<?php

AAFW::import ('jp.aainc.classes.brandco.cp.CpNewSkeletonCreator');

class CpNewSkeletonCreatorTest extends BaseTest {

    /** @var CpNewSkeletonCreator $target */
    private $target;
    /** @var CpFlowService $cp_flow_service */
    private $cp_flow_service;

    public function setUp() {
        $this->target = new CpNewSkeletonCreator();
        $this->cp_flow_service = aafwServiceFactory::create("CpFlowService");
    }

    public function testCreate_typeCampaign() {
        $brand = $this->entity('Brands');
        $data = array(
            'groupCount' => 2,
            'group1' => '0,5,9',
            'group2' => '3,7',
        );
        $cps_type = cp::TYPE_CAMPAIGN;
        $join_limit_flg = cp::JOIN_LIMIT_OFF;

        $this->target->create($brand->id, $data, $cps_type, $join_limit_flg);
        $cp = $this->cp_flow_service->getDraftCpsByBrandIdAndArchiveFlg($brand->id)->toArray()[0];

        $cp_actions = $this->cp_flow_service->getCpActionsByCpId($cp->id);
        foreach($cp_actions as $cp_action) {
            $actual_actions[] = array(
                'type' => $cp_action->type,
                'order_no' => $cp_action->order_no
            );
        }

        $expect_actions = array(
            array(
                'type' => CpAction::TYPE_ENTRY,
                'order_no' => 1
            ),
            array(
                'type' => CpAction::TYPE_QUESTIONNAIRE,
                'order_no' => 2
            ),
            array(
                'type' => CpAction::TYPE_JOIN_FINISH,
                'order_no' => 3
            ),
            array(
                'type' => CpAction::TYPE_ANNOUNCE,
                'order_no' => 1
            ),
            array(
                'type' => CpAction::TYPE_FREE_ANSWER,
                'order_no' => 2
            ),
        );
        $this->assertEquals($expect_actions, $actual_actions);
    }

    public function testCreate_typeMessage() {
        $brand = $this->entity('Brands');
        $data = array(
            'groupCount' => 2,
            'group1' => '1,5,2',
            'group2' => '4,7',
        );
        $cps_type = cp::TYPE_MESSAGE;
        $join_limit_flg = cp::JOIN_LIMIT_OFF;

        $this->target->create($brand->id, $data, $cps_type, $join_limit_flg);
        $cp = $this->cp_flow_service->getPublicCpsByBrandId($brand->id)->toArray()[0];

        $cp_actions = $this->cp_flow_service->getCpActionsByCpId($cp->id);
        foreach($cp_actions as $cp_action) {
            $actual_actions[] = array(
                'type' => $cp_action->type,
                'order_no' => $cp_action->order_no
            );
        }

        $expect_actions = array(
            array(
                'type' => CpAction::TYPE_MESSAGE,
                'order_no' => 1
            ),
            array(
                'type' => CpAction::TYPE_QUESTIONNAIRE,
                'order_no' => 2
            ),
            array(
                'type' => CpAction::TYPE_PHOTO,
                'order_no' => 3
            ),
            array(
                'type' => CpAction::TYPE_SHIPPING_ADDRESS,
                'order_no' => 1
            ),
            array(
                'type' => CpAction::TYPE_FREE_ANSWER,
                'order_no' => 2
            ),
        );
        $this->assertEquals($expect_actions, $actual_actions);
    }

    public function testUpdateAction_notCpId() {
        $this->assertNotTrue($this->target->updateAction(null));
    }

    public function testUpdateAction_notOldGroups() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => Cp::TYPE_CAMPAIGN));
        $this->assertNotTrue($this->target->updateAction($cp->id));
    }

    public function testUpdateAction_cpOpen() {
        $brand = $this->entity('Brands');

        $cp1 = $this->entity('Cps', array('brand_id' => $brand->id, 'status'=> 1));

        $cp_action_group1 = $this->entity('CpActionGroups', array('cp_id' => $cp1->id, 'order_no' => 1));
        $cp_action_group2 = $this->entity('CpActionGroups', array('cp_id' => $cp1->id, 'order_no' => 2));
        $cp_action_group3 = $this->entity('CpActionGroups', array('cp_id' => $cp1->id, 'order_no' => 3));

        $cp_actions = $this->entities('CpActions', array(
                array('cp_action_group_id' => $cp_action_group1->id, 'type' => CpAction::TYPE_ENTRY, 'order_no' => 1),
                array('cp_action_group_id' => $cp_action_group1->id, 'type' => CpAction::TYPE_PHOTO, 'order_no' => 2),
                array('cp_action_group_id' => $cp_action_group2->id, 'type' => CpAction::TYPE_MESSAGE, 'order_no' => 1),
                array('cp_action_group_id' => $cp_action_group2->id, 'type' => CpAction::TYPE_MOVIE, 'order_no' => 2),
                array('cp_action_group_id' => $cp_action_group3->id, 'type' => CpAction::TYPE_POPULAR_VOTE, 'order_no' => 1),
            )
        );

        $this->entity('CpPhotoActions', array('cp_action_id' => $cp_actions[1]->id));
        $this->entity('CpMessageActions', array('cp_action_id' => $cp_actions[2]->id));
        $this->entity('CpMovieActions', array('cp_action_id' => $cp_actions[3]->id));
        $this->entity('CpPopularVoteActions', array('cp_action_id' => $cp_actions[4]->id));

        $this->entities('CpNextActions',  array(
                array('cp_action_id' => $cp_actions[0]->id, 'cp_next_action_id' => $cp_actions[1]->id),
                array('cp_action_id' => $cp_actions[2]->id, 'cp_next_action_id' => $cp_actions[3]->id),
            )
        );

        $data = array(
            'groupCount' => 3,
            'group1' => '0,9',
            'group2' => '1,3',
            'group3' => '23',
            'groupUpdate' => $cp_action_group1->id.',-1,'.$cp_action_group3->id,
            'actionUpdate1' => $cp_actions[0]->id.',-1',
            'actionUpdate2' => $cp_actions[2]->id.',-1',
            'actionUpdate3' => $cp_actions[4]->id,
        );

        $this->target->updateAction($cp1->id, $data);

        $cp_actual_actions = $this->cp_flow_service->getCpActionsByCpId($cp1->id);
        foreach($cp_actual_actions as $cp_actual_action) {
            $actual_actions[] = array(
                'type' => $cp_actual_action->type,
                'order_no' => (int)$cp_actual_action->order_no
            );
        }

        $expect_actions = array(
            array(
                'type' => CpAction::TYPE_ENTRY,
                'order_no' => 1
            ),
            array(
                'type' => CpAction::TYPE_JOIN_FINISH,
                'order_no' => 2
            ),
            array(
                'type' => CpAction::TYPE_MESSAGE,
                'order_no' => 1
            ),
            array(
                'type' => CpAction::TYPE_ANNOUNCE,
                'order_no' => 2
            ),
            array(
                'type' => CpAction::TYPE_POPULAR_VOTE,
                'order_no' => 1
            ),
        );
        $this->assertEquals($expect_actions, $actual_actions);
    }
}
