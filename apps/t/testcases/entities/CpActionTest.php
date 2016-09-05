<?php

AAFW::import ('jp.aainc.aafw.classes.entities.CpAction');

class CpActionTest extends BaseTest {

    /** @test */
    public function isFirstGroupAction_FirstGroupである() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp_action_group->order_no = 1;
        $this->save('CpActionGroups', $cp_action_group);

        $this->assertTrue($cp_action->isFirstGroupAction());
    }

    /** @test */
    public function isFirstGroupAction_FirstGroupではない() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp_action_group->order_no = 11;
        $this->save('CpActionGroups', $cp_action_group);

        $this->assertFalse($cp_action->isFirstGroupAction());
    }

    /** @test */
    public function isFirstGroupAction_例外が発生すること() {
        $store = aafwEntityStoreFactory::create('CpActions');
        $cp_action = $store->createEmptyObject();

        try {
            $cp_action->isFirstGroupAction();
            $this->fail('例外がスローされませんでした');
        } catch (aafwException $e) {
            $this->assertTrue(true);
        }
    }

    /** @test */
    public function isActive_END_TYPE_Original_OK() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp_action->end_type = CpAction::END_TYPE_ORIGINAL;
        $cp_action->end_at = '2030-07-17 10:00:00';

        $this->assertTrue($cp_action->isActive());
    }

    /** @test */
    public function isActive_END_TYPE_Original_NG() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp_action->end_type = CpAction::END_TYPE_ORIGINAL;
        $cp_action->end_at = '2015-06-17 10:00:00';

        $this->assertFalse($cp_action->isActive());
    }

    /** @test */
    public function isActive_END_TYPE_NONE_OK() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp_action->end_type = CpAction::END_TYPE_NONE;

        $this->assertTrue($cp_action->isActive());
    }

    /** @test */
    public function isActive_END_TYPE_CP_CAMPAIGN_OK() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->status = Cp::CAMPAIGN_STATUS_OPEN;
        $dt = new Datetime();
        $cp->public_date = $dt->modify('-1 days')->format('Y-m-d H:i:s');
        $cp->start_date = $cp->public_date;
        $cp->end_date = $dt->modify('+2 days')->format('Y-m-d H:i:s');
        $cp->type = CP::TYPE_CAMPAIGN;
        $this->save('Cps', $cp);

        $cp_action->end_type = CpAction::END_TYPE_CP;
        $this->save('CpActions', $cp_action);

        $this->assertTrue($cp_action->isActive());
    }

    /** @test */
    public function isActive_END_TYPE_CP_CAMPAIGN_NG() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->status = Cp::CAMPAIGN_STATUS_DRAFT;
        $cp->type = CP::TYPE_CAMPAIGN;
        $this->save('Cps', $cp);

        $cp_action->end_type = CpAction::END_TYPE_CP;
        $this->save('CpActions', $cp_action);

        $this->assertFalse($cp_action->isActive());
    }

    /** @test */
    public function isActive_END_TYPE_CP_MESSAGE_OK() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        #$cp->status = Cp::CAMPAIGN_STATUS_OPEN;
        $cp->type = CP::TYPE_MESSAGE;
        $this->save('Cps', $cp);

        $cp_action->end_type = CpAction::END_TYPE_CP;
        $this->save('CpActions', $cp_action);

        $this->assertTrue($cp_action->isActive());
    }

    /** @test */
    public function isActive_END_TYPEがデフォルト値（−１）() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp_action->end_type = -1;
        $this->save('CpActions', $cp_action);

        $this->assertTrue($cp_action->isActive());
    }

    /** @test */
    public function isActive_CP_DEMO() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->status = CP::STATUS_DEMO;
        $cp->type = CP::TYPE_CAMPAIGN;
        $this->save('Cps', $cp);

        $cp_action->end_type = CpAction::END_TYPE_CP;
        $this->save('CpActions', $cp_action);

        $this->assertTrue($cp_action->isActive());
    }

    /** @test */
    public function isEndTypeDraft_締め切り日設定が未登録であること() {
        $store = aafwEntityStoreFactory::create('CpActions');
        $cp_action = $store->createEmptyObject();

        $cp_action->end_type = -1;

        $this->assertTrue($cp_action->isEndTypeDraft());
    }

    /** @test */
    public function isEndTypeDraft_締め切り日設定が登録済みであること() {
        $store = aafwEntityStoreFactory::create('CpActions');
        $cp_action = $store->createEmptyObject();

        $cp_action->end_type = 0;

        $this->assertFalse($cp_action->isEndTypeDraft());
    }

    /** @test */
    public function getDefaultEndType_キャンペーン_ok() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->type = Cp::TYPE_CAMPAIGN;
        $this->save('Cps', $cp);

        $this->assertEquals(0, $cp_action->getDefaultEndType());
    }

    /** @test */
    public function getDefaultEndType_キャンペーン_ステップグループ_1() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->type = Cp::TYPE_CAMPAIGN;
        $this->save('Cps', $cp);

        $cp_action_group->order_no = 1;
        $this->save('CpActionGroups', $cp_action_group);

        $this->assertEquals(1, $cp_action->getDefaultEndType());
    }

    /** @test */
    public function getDefaultEndType_キャンペーン_ステップグループ_2() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->type = Cp::TYPE_CAMPAIGN;
        $this->save('Cps', $cp);

        $cp_action_group->order_no = 2;
        $this->save('CpActionGroups', $cp_action_group);

        $this->assertEquals(0, $cp_action->getDefaultEndType());
    }

    /** @test */
    public function getDefaultEndType_メッセージ_ok() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->type = Cp::TYPE_MESSAGE;
        $this->save('Cps', $cp);

        $this->assertEquals(0, $cp_action->getDefaultEndType());
    }

    /** @test */
    public function getDefaultEndType_メッセージ_ステップグループ_1() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->type = Cp::TYPE_MESSAGE;
        $this->save('Cps', $cp);

        $cp_action_group->order_no = 1;
        $this->save('CpActionGroups', $cp_action_group);

        $this->assertEquals(0, $cp_action->getDefaultEndType());
    }

    /** @test */
    public function getDefaultEndType_メッセージ_ステップグループ_2() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->type = Cp::TYPE_MESSAGE;
        $this->save('Cps', $cp);

        $cp_action_group->order_no = 2;
        $this->save('CpActionGroups', $cp_action_group);

        $this->assertEquals(0, $cp_action->getDefaultEndType());
    }

    /** @test */
    public function getEndType_CpActionの属性から取得() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->type = Cp::TYPE_CAMPAIGN;
        $this->save('Cps', $cp);

        $cp_action->end_type = CpAction::END_TYPE_CP;
        $this->save('CpActionGroups', $cp_action_group);

        $this->assertEquals(CpAction::END_TYPE_CP, $cp_action->getEndType());
    }

    /** @test */
    public function getEndType_キャンペーン_0() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->type = Cp::TYPE_CAMPAIGN;
        $this->save('Cps', $cp);

        $cp_action->end_type = 0;
        $this->save('CpActions', $cp_action);

        $this->assertEquals(0, $cp_action->getEndType());
    }

    /** @test */
    public function getEndType_キャンペーン_1() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->type = Cp::TYPE_CAMPAIGN;
        $this->save('Cps', $cp);

        $cp_action->end_type = 1;
        $this->save('CpActions', $cp_action);

        $this->assertEquals(1, $cp_action->getEndType());
    }

    /** @test */
    public function getEndtype_キャンペーン_DefaultEndType_0() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newbrandtoaction();

        $cp->type = Cp::TYPE_CAMPAIGN;
        $this->save('Cps', $cp);

        $cp_action->end_type = -1;
        $this->save('CpActions', $cp_action);

        $this->assertEquals(0, $cp_action->getendtype());
    }

    /** @test */
    public function getEndType_キャンペーン_DefaultEndType_1() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->type = Cp::TYPE_CAMPAIGN;
        $this->save('Cps', $cp);

        $cp_action_group->order_no = 1;
        $this->save('CpActionGroups', $cp_action_group);

        $cp_action->end_type = -1;
        $this->save('CpActions', $cp_action);

        $this->assertEquals(1, $cp_action->getEndType());
    }

    /** @test */
    public function getEndType_メッセージ_0() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->type = Cp::TYPE_MESSAGE;
        $this->save('Cps', $cp);

        $cp_action->end_type = 0;
        $this->save('CpActions', $cp_action);

        $this->assertEquals(0, $cp_action->getEndType());
    }

    /** @test */
    public function getEndType_メッセージ_1() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $cp->type = Cp::TYPE_MESSAGE;
        $this->save('Cps', $cp);

        $cp_action->end_type = 1;
        $this->save('CpActions', $cp_action);

        $this->assertEquals(1, $cp_action->getEndType());
    }

    /** @test */
    public function getEndType_メッセージ_DefaultEndType_0() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newbrandtoaction();

        $cp->type = Cp::TYPE_MESSAGE;
        $this->save('Cps', $cp);

        $cp_action->end_type = -1;
        $this->save('CpActions', $cp_action);

        $this->assertEquals(0, $cp_action->getendtype());
    }

    /** @test */
    public function isOpeningCpAction_typeLegalOpeningAction() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newbrandtoaction(Cp::TYPE_CAMPAIGN, CpAction::TYPE_ENTRY);

        $cp_action->order_no = 1;
        $this->save('CpActions', $cp_action);

        $cp_action_group->order_no = 1;
        $this->save('CpActionGroups', $cp_action_group);

        $this->assertTrue($cp_action->isOpeningCpAction());
    }

    /** @test */
    public function isOpeningCpAction_typeIlegalOpeningAction() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newbrandtoaction(Cp::TYPE_CAMPAIGN, CpAction::TYPE_MESSAGE);

        $cp_action->order_no = 1;
        $this->save('CpActions', $cp_action);

        $cp_action_group->order_no = 1;
        $this->save('CpActionGroups', $cp_action_group);

        $this->assertNotTrue($cp_action->isOpeningCpAction());
    }

    /** @test */
    public function isOpeningCpAction_typeActionNo2() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newbrandtoaction(Cp::TYPE_CAMPAIGN, CpAction::TYPE_ENTRY);

        $cp_action->order_no = 2;
        $this->save('CpActions', $cp_action);

        $cp_action_group->order_no = 1;
        $this->save('CpActionGroups', $cp_action_group);

        $this->assertNotTrue($cp_action->isOpeningCpAction());
    }

    /** @test */
    public function isOpeningCpAction_typeGroupNo2() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newbrandtoaction(Cp::TYPE_CAMPAIGN, CpAction::TYPE_ENTRY);

        $cp_action->order_no = 1;
        $this->save('CpActions', $cp_action);

        $cp_action_group->order_no = 2;
        $this->save('CpActionGroups', $cp_action_group);

        $this->assertNotTrue($cp_action->isOpeningCpAction());
    }

    /** @test */
    public function isOpeningCpAction_typeMessageCp() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newbrandtoaction(Cp::TYPE_MESSAGE, CpAction::TYPE_ENTRY);

        $cp_action->order_no = 1;
        $this->save('CpActions', $cp_action);

        $cp_action_group->order_no = 1;
        $this->save('CpActionGroups', $cp_action_group);

        $this->assertNotTrue($cp_action->isOpeningCpAction());
    }

}
