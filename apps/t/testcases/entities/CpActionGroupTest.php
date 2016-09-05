<?php

AAFW::import('jp.aainc.aafw.classes.entities.CpActionGroup');

class CpActionGroupTest extends BaseTest {

    /** @test */
    public function isFirstGroup_最初のステップグループであること() {
        $store = aafwEntityStoreFactory::create('CpActionGroups');
        $cp_action_group = $store->createEmptyObject();

        $cp_action_group->order_no = 1;

        $this->assertTrue($cp_action_group->isFirstGroup());
    }

    /** @test */
    public function isFirstGroup_最初のステップグループではないこと() {
        $store = aafwEntityStoreFactory::create('CpActionGroups');
        $cp_action_group = $store->createEmptyObject();

        $cp_action_group->order_no = '';

        $this->assertFalse($cp_action_group->isFirstGroup());
    }
}
