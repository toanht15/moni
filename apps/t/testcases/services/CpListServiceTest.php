<?php
AAFW::import ('jp.aainc.classes.services.CpListService');

class CpListServiceTest extends BaseTest {

    /** @var  CpListService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("CpListService");
    }

    public function testGetListPublicCp01_argIsNull() {
        $this->assertNotTrue($this->target->getListPublicCp(null));
    }

    public function testGetListPublicCp02_notMatch() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id,'order_no' => 1));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id,'order_no' => 1));
        $cps = array();
        $cps[$cp->id][$cp_action_group->id]['group_order_no'] = $cp_action_group->order_no;
        $cps[$cp->id][$cp_action_group->id][$cp_action->id]['action_order_no'] = $cp_action->order_no;
        $cps[$cp->id][$cp_action_group->id][$cp_action->id]['type'] = $cp_action->type;

        $cp = $this->entity('Cps', array('brand_id' => $brand->id));

        $this->assertEquals(array(), $this->target->getListPublicCp(array($cp->id)));
    }

    public function testGetListPublicCp04_matched() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id,'order_no' => 1));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id,'order_no' => 1));
        $cps = array();
        $cps[$cp->id][$cp_action_group->id]['group_order_no'] = $cp_action_group->order_no;
        $cps[$cp->id][$cp_action_group->id][$cp_action->id]['action_order_no'] = $cp_action->order_no;
        $cps[$cp->id][$cp_action_group->id][$cp_action->id]['type'] = $cp_action->type;
        $this->assertEquals($cps, $this->target->getListPublicCp(array($cp->id)));
    }

    public function testGetStepNo() {
        $group = array(
            array(
                'action_order_no' => 1
            ),
            array(
                'action_order_no' => 2
            ),
        );
        $this->assertEquals(array(1,2), $this->target->getStepNo($group));
    }
}