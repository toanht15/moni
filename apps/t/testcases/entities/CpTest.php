<?php

AAFW::import ('jp.aainc.aafw.classes.entities.Cp');
AAFW::import ('jp.aainc.aafw.classes.brandco.cp.CpInstantWinActionManager');
AAFW::import ('jp.aainc.aafw.classes.services.instant_win.InstantWinPrizeService');

class CpTest extends BaseTest {

    public function testIsOverLimitWinner_whenEqualsToLimitAndAnnounceFirst() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('user_id' => $user->id, 'cp_id' => $cp->id));
        $cp_user_action_status = $this->entity('CpUserActionStatuses', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id, 'status' => 1));

        $cp_action_group->order_no = 1;
        $this->save('CpActionGroups', $cp_action_group);

        $cp_action->order_no = 1;
        $this->save('CpActions', $cp_action);

        $cp->winner_count = 1;
        $cp->selection_method = CpCreator::ANNOUNCE_FIRST;
        $this->save('Cps', $cp);

        $this->assertTrue($cp->isOverLimitWinner());
    }


    public function testIsOverLimitWinner_whenNotEqualsToLimitAndAnnounceFirst() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('user_id' => $user->id, 'cp_id' => $cp->id));
        $cp_user_action_status = $this->entity('CpUserActionStatuses', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id, 'status' => 0));

        $cp_action_group->order_no = 1;
        $this->save('CpActionGroups', $cp_action_group);

        $cp_action->order_no = 1;
        $this->save('CpActions', $cp_action);

        $cp->winner_count = 1;
        $cp->selection_method = CpCreator::ANNOUNCE_FIRST;
        $this->save('Cps', $cp);

        $this->assertFalse($cp->isOverLimitWinner());
    }

    function testIsOverLimitWinner_whenEqualsToLimitAndLottery() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('user_id' => $user->id, 'cp_id' => $cp->id));

        $manager = new CpInstantWinActionManager();
        list($new_cp_action, $concrete_cp_action) = $manager->createCpActions($cp_action_group->id, CpAction::TYPE_INSTANT_WIN, 0, 1);

        $cp->winner_count = 1;
        $cp->selection_method = CpCreator::ANNOUNCE_LOTTERY;
        $this->save('Cps', $cp);

        $prize_service = new InstantWinPrizeService();
        $prize = $prize_service->getInstantWinPrizeByPrizeStatus($concrete_cp_action->id, InstantWinPrizes::PRIZE_STATUS_PASS);
        $prize->winner_count = 1;
        $this->save('InstantWinPrizes', $prize);

        $this->assertTrue($cp->isOverLimitWinner());
    }

    public function testIsOverLimitWinner_whenNotEqualsToLimitAndLottery() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('user_id' => $user->id, 'cp_id' => $cp->id));

        $manager = new CpInstantWinActionManager();
        list($new_cp_action, $concrete_cp_action) = $manager->createCpActions($cp_action_group->id, CpAction::TYPE_INSTANT_WIN, 0, 1);

        $cp->winner_count = 1;
        $cp->selection_method = CpCreator::ANNOUNCE_LOTTERY;
        $this->save('Cps', $cp);

        $prize_service = new InstantWinPrizeService();
        $prize = $prize_service->getInstantWinPrizeByPrizeStatus($concrete_cp_action->id, InstantWinPrizes::PRIZE_STATUS_PASS);
        $prize->winner_count = 0;
        $this->save('InstantWinPrizes', $prize);

        $this->assertFalse($cp->isOverLimitWinner());
    }

    /** @test */
    public function isCpTypeCampaign_campaignであること() {
        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();

        $cp->type = Cp::TYPE_CAMPAIGN;

        $this->assertTrue($cp->isCpTypeCampaign());
    }

    /** @test */
    public function isCpTypeCampaign_campaignではないこと() {
        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();

        $this->assertFalse($cp->isCpTypeCampaign());
    }

    /** @test */
    public function isCpTypeCampaign_messageであること() {
        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();

        $cp->type = Cp::TYPE_MESSAGE;

        $this->assertTrue($cp->isCpTypeMessage());
    }

    /** @test */
    public function isCpTypeCampaign_messageではないこと() {
        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();

        $this->assertFalse($cp->isCpTypeMessage());
    }

    public function testGetUrl01_whenNoArg() {
        $brand = $this->entity("Brands", array("directory_name" => "directory"));
        $cp = $this->entity("Cps", array("brand_id" => $brand->id));
        $this->assertEquals("http://brandcotest.com/directory/campaigns/{$cp->id}", $cp->getUrl());
    }

    public function testGetUrl02_whenSecure() {
        $brand = $this->entity("Brands", array("directory_name" => "directory"));
        $cp = $this->entity("Cps", array("brand_id" => $brand->id));
        $this->assertEquals("https://brandcotest.com/directory/campaigns/{$cp->id}", $cp->getUrl(true));
    }

    public function testGetUrl03_whenSecureAndPassBrand() {
        $brand = $this->entity("Brands", array("directory_name" => "directory"));
        $cp = $this->entity("Cps", array("brand_id" => $brand->id));
        $this->assertEquals("https://brandcotest.com/directory/campaigns/{$cp->id}", $cp->getUrl(true, $brand));
    }

    /** @test */
    public function isDemo_OK() {
        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();
        $cp->status = CP::STATUS_DEMO;

        $this->assertTrue($cp->isDemo());
    }

    /** @test */
    public function isDemo_NG() {
        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();
        $cp->status = CP::STATUS_FIX;

        $this->assertFalse($cp->isDemo());
    }

    /**
     * キャンペーンのステータスが発表待ち
     */
    public function testIsCampaignTermFinished01_whenCampaignStatusWaitAnnounce() {
        $date = new DateTime();
        $endDate      = $date->modify("-1 day")->format('Y-m-d H:i:s');
        $announceDate = $date->modify("+2 day")->format('Y-m-d H:i:s');

        $store = aafwEntityStoreFactory::create('Cps');
        $campaign = $store->createEmptyObject();
        $campaign->end_date      = $endDate;
        $campaign->announce_date = $announceDate;
        $campaign->status        = Cp::STATUS_FIX;

        $this->assertTrue($campaign->isCampaignTermFinished());
    }

    /**
     * キャンペーンのステータスがクローズ
     */
    public function testIsCampaignTermFinished02_whenCampaignStatusClose() {
        $date = new DateTime();
        $endDate      = $date->modify("-2 day")->format('Y-m-d H:i:s');
        $announceDate = $date->modify("-1 day")->format('Y-m-d H:i:s');

        $store = aafwEntityStoreFactory::create('Cps');
        $campaign = $store->createEmptyObject();
        $campaign->end_date      = $endDate;
        $campaign->announce_date = $announceDate;
        $campaign->status        = Cp::STATUS_FIX;

        $this->assertTrue($campaign->isCampaignTermFinished());
    }

    /**
     * キャンペーンのステータスが公開中
     */
    public function testIsCampaignTermFinished03_whenCampaignStatusOpen() {
        $date = new DateTime();
        $publicDate = $date->modify("-1 day")->format('Y-m-d H:i:s');
        $endDate    = $date->modify("+2 day")->format('Y-m-d H:i:s');

        $store = aafwEntityStoreFactory::create('Cps');
        $campaign = $store->createEmptyObject();
        $campaign->public_date   = $publicDate;
        $campaign->end_date      = $endDate;
        $campaign->status        = Cp::STATUS_FIX;

        $this->assertFalse($campaign->isCampaignTermFinished());
    }

    public function testCpStatus01_isDraftCp() {
        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();
        $cp->status        = Cp::STATUS_DRAFT;

        $this->assertEquals(Cp::CAMPAIGN_STATUS_DRAFT, $cp->getStatus());
    }

    public function testCpStatus02_isScheduledCp() {
        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();
        $cp->status        = Cp::STATUS_SCHEDULE;

        $this->assertEquals(Cp::CAMPAIGN_STATUS_SCHEDULE, $cp->getStatus());
    }

    public function testCpStatus03_isDemoCp() {
        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();
        $cp->status        = Cp::STATUS_DEMO;

        $this->assertEquals(Cp::CAMPAIGN_STATUS_DEMO, $cp->getStatus());
    }

    public function testCpStatus04_isClosedCp() {
        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();
        $cp->status        = Cp::STATUS_CLOSE;

        $this->assertEquals(Cp::CAMPAIGN_STATUS_CP_PAGE_CLOSED, $cp->getStatus());
    }

    public function testCpStatus05_isClosedAnnounceFirstCpOnOverLimitWinner() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('user_id' => $user->id, 'cp_id' => $cp->id));
        $cp_user_action_status = $this->entity('CpUserActionStatuses', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id, 'status' => 1));

        $cp_action_group->order_no = 1;
        $this->save('CpActionGroups', $cp_action_group);

        $cp_action->order_no = 1;
        $this->save('CpActions', $cp_action);

        $cp->winner_count = 1;
        $cp->status = Cp::STATUS_FIX;
        $cp->selection_method = CpCreator::ANNOUNCE_FIRST;
        $this->save('Cps', $cp);

        $this->assertEquals(Cp::CAMPAIGN_STATUS_CLOSE, $cp->getStatus());
    }

    public function testCpStatus06_isClosedInstantWinCpOnOverLimitWinner() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('user_id' => $user->id, 'cp_id' => $cp->id));

        $manager = new CpInstantWinActionManager();
        list($new_cp_action, $concrete_cp_action) = $manager->createCpActions($cp_action_group->id, CpAction::TYPE_INSTANT_WIN, 0, 1);

        $cp->winner_count = 1;
        $cp->status = Cp::STATUS_FIX;
        $cp->selection_method = CpCreator::ANNOUNCE_LOTTERY;
        $this->save('Cps', $cp);

        $prize_service = new InstantWinPrizeService();
        $prize = $prize_service->getInstantWinPrizeByPrizeStatus($concrete_cp_action->id, InstantWinPrizes::PRIZE_STATUS_PASS);
        $prize->winner_count = 1;
        $this->save('InstantWinPrizes', $prize);

        $this->assertEquals(Cp::CAMPAIGN_STATUS_CLOSE, $cp->getStatus());
    }

    public function testCpStatus07_isOpenCp() {
        $date = new DateTime();
        $publicDate = $date->modify("-1 day")->format('Y-m-d H:i:s');
        $endDate    = $date->modify("+2 day")->format('Y-m-d H:i:s');

        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();
        $cp->public_date   = $publicDate;
        $cp->end_date      = $endDate;
        $cp->status        = Cp::STATUS_FIX;

        $this->assertEquals(Cp::CAMPAIGN_STATUS_OPEN, $cp->getStatus());
    }

    public function testCpStatus08_isOpenCp() {
        $date = new DateTime();
        $publicDate = $date->modify("-1 day")->format('Y-m-d H:i:s');

        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();
        $cp->public_date   = $publicDate;
        $cp->status        = Cp::STATUS_FIX;
        $cp->permanent_flg = Cp::PERMANENT_FLG_ON;

        $this->assertEquals(Cp::CAMPAIGN_STATUS_OPEN, $cp->getStatus());
    }

    public function testCpStatus09_isWaitingAnnounceCp() {
        $date = new DateTime();
        $endDate = $date->modify("-1 day")->format('Y-m-d H:i:s');
        $announceDate = $date->modify("+2 day")->format('Y-m-d H:i:s');

        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();
        $cp->end_date      = $endDate;
        $cp->announce_date   = $announceDate;
        $cp->status        = Cp::STATUS_FIX;

        $this->assertEquals(Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE, $cp->getStatus());
    }

    public function testCpStatus10_isClosedCp() {
        $date = new DateTime();
        $announceDate = $date->modify("-1 day")->format('Y-m-d H:i:s');

        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();
        $cp->announce_date   = $announceDate;
        $cp->status        = Cp::STATUS_FIX;

        $this->assertEquals(Cp::CAMPAIGN_STATUS_CLOSE, $cp->getStatus());
    }

    public function testCpStatus11_isIllegalCpStatus() {
        $date = new DateTime();
        $publicDate = $date->modify("+1 day")->format('Y-m-d H:i:s');
        $endDate = $date->modify("+2 day")->format('Y-m-d H:i:s');
        $announceDate = $date->modify("+3 day")->format('Y-m-d H:i:s');

        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();
        $cp->public_date   = $publicDate;
        $cp->end_date      = $endDate;
        $cp->announce_date   = $announceDate;
        $cp->status        = Cp::STATUS_FIX;

        $this->assertEquals(Cp::CAMPAIGN_STATUS_DRAFT, $cp->getStatus());
    }

    public function testCpStatus12_isClosedCp() {
        $date = new DateTime();
        $endDate = $date->modify("-1 day")->format('Y-m-d H:i:s');
        $announceDate = $date->modify("+2 day")->format('Y-m-d H:i:s');

        $store = aafwEntityStoreFactory::create('Cps');
        $cp = $store->createEmptyObject();
        $cp->end_date      = $endDate;
        $cp->announce_date   = $announceDate;
        $cp->status        = Cp::STATUS_FIX;
        $cp->selection_method = CpCreator::ANNOUNCE_NON_INCENTIVE;

        $this->assertEquals(Cp::CAMPAIGN_STATUS_CLOSE, $cp->getStatus());
    }
}
