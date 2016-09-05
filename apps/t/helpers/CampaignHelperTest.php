<?php
AAFW::import('jp.aainc.t.helpers.CampaignHelper');
AAFW::import('jp.aainc.classes.entities.Cp');

class CampaignHelperTest extends BaseTest {

    /** @var CampaignHelper $campaign_helper */
    private $campaign_helper;
    private $brand_id = 1;

    public function setUp() {
        $this->campaign_helper = new CampaignHelper();
    }

    /**
     * CP作成
     * 基本（参加、参加完了、当選発表）
     */
    public function testCreateCampaignTest_Cp1() {
        $this->campaign_helper->cleanupCampaigns();

        $cp_data = array('cps_type' => 1, 'skeleton_type' => 4, 'announce_type' => 0, 'groupCount' => 2, 'group1' => '0,9', 'group2' => '3');
        $cp = $this->campaign_helper->createCampaign($this->brand_id, $cp_data);

        $this->assertEquals(2, $cp->getCountCpActionGroups());

        $groups_array = $cp->getCpActionGroups()->toArray();

        $cp_actions_array = $groups_array[0]->getCpActions()->toArray();
        $this->assertEquals(0, $cp_actions_array[0]->type);
        $this->assertEquals(9, $cp_actions_array[1]->type);

        $cp_actions_array = $groups_array[1]->getCpActions()->toArray();
        $this->assertEquals(3, $cp_actions_array[0]->type);

        $this->campaign_helper->cleanupCampaigns();
    }

    /**
     * Message作成
     * 基本
     */
    public function testCreateCampaignTest_Message1() {
        $this->campaign_helper->cleanupCampaigns();

        $cp_data = array('cps_type' => 2, 'skeleton_type' => 4, 'groupCount' => 1, 'group1' => '1');
        $cp = $this->campaign_helper->createCampaign($this->brand_id, $cp_data, Cp::TYPE_MESSAGE);

        $this->assertEquals(1, $cp->getCountCpActionGroups());

        $groups_array = $cp->getCpActionGroups()->toArray();

        $cp_actions_array = $groups_array[0]->getCpActions()->toArray();
        $this->assertEquals(1, $cp_actions_array[0]->type);

        $this->campaign_helper->cleanupCampaigns();
    }

    /**
     * Message作成
     * 写真
     */
    public function testCreateCampaignTest_Message2() {
        $this->campaign_helper->cleanupCampaigns();

        $cp_data = array('cps_type' => 2, 'skeleton_type' => 4, 'groupCount' => 1, 'group1' => '1,2');
        $cp = $this->campaign_helper->createCampaign($this->brand_id, $cp_data, Cp::TYPE_MESSAGE);

        $this->assertEquals(1, $cp->getCountCpActionGroups());

        $groups_array = $cp->getCpActionGroups()->toArray();

        $cp_actions_array = $groups_array[0]->getCpActions()->toArray();
        $this->assertEquals(1, $cp_actions_array[0]->type);
        $this->assertEquals(2, $cp_actions_array[1]->type);

        $this->campaign_helper->cleanupCampaigns();
    }

    /**
     * Message
     * グループ2,写真
     */
    public function testCreateCampaignTest_Message3() {
        $this->campaign_helper->cleanupCampaigns();

        $cp_data = array('cps_type' => 2, 'skeleton_type' => 4, 'groupCount' => 2, 'group1' => '1', 'group2' => '2');
        $cp = $this->campaign_helper->createCampaign($this->brand_id, $cp_data, Cp::TYPE_MESSAGE);

        $this->assertEquals(2, $cp->getCountCpActionGroups());

        $groups_array = $cp->getCpActionGroups()->toArray();

        $cp_actions_array = $groups_array[0]->getCpActions()->toArray();
        $this->assertEquals(1, $cp_actions_array[0]->type);

        $cp_actions_array = $groups_array[1]->getCpActions()->toArray();
        $this->assertEquals(2, $cp_actions_array[0]->type);

        $this->campaign_helper->cleanupCampaigns();
    }

    /**
     * CP全削除
     * 基本（参加、参加完了、当選発表）
     */
    public function testCleanUpCampaigns() {
        $this->campaign_helper->cleanupCampaigns();

        $cp_data = array('cps_type' => 1, 'skeleton_type' => 4, 'announce_type' => 0, 'groupCount' => 2, 'group1' => '0,9', 'group2' => '3');
        $cp = $this->campaign_helper->createCampaign($this->brand_id, $cp_data);

        $this->assertEquals(2, $cp->getCountCpActionGroups());

        $groups_array = $cp->getCpActionGroups()->toArray();

        $cp_actions_array = $groups_array[0]->getCpActions()->toArray();
        $this->assertEquals(0, $cp_actions_array[0]->type);
        $this->assertEquals(9, $cp_actions_array[1]->type);

        $cp_actions_array = $groups_array[1]->getCpActions()->toArray();
        $this->assertEquals(3, $cp_actions_array[0]->type);

        $this->campaign_helper->cleanupCampaigns();

        $cps = aafwEntityStoreFactory::create('Cps');
        $this->assertEquals(0,$cps->count());
    }

    /**
     * CP削除ByCpId
     */
    public function testCleanUpCampaignByCpId() {
        $this->campaign_helper->cleanupCampaigns();

        $cp_data = array('cps_type' => 1, 'skeleton_type' => 4, 'announce_type' => 0, 'groupCount' => 2, 'group1' => '0,9', 'group2' => '3');
        $cp = $this->campaign_helper->createCampaign($this->brand_id, $cp_data);

        $this->assertEquals(2, $cp->getCountCpActionGroups());

        $groups_array = $cp->getCpActionGroups()->toArray();

        $cp_actions_array = $groups_array[0]->getCpActions()->toArray();
        $this->assertEquals(0, $cp_actions_array[0]->type);
        $this->assertEquals(9, $cp_actions_array[1]->type);

        $cp_actions_array = $groups_array[1]->getCpActions()->toArray();
        $this->assertEquals(3, $cp_actions_array[0]->type);

        $this->campaign_helper->cleanupCampaignByCpId($cp->id);

        $cps = aafwEntityStoreFactory::create('Cps');
        $this->assertEquals(0,$cps->count(array('id' => $cp->id)));
    }

    /**
     * CP削除ByBrandId
     */
    public function testCleanUpCampaignByBrandId() {
        $this->campaign_helper->cleanupCampaigns();

        $cp_data = array('cps_type' => 1, 'skeleton_type' => 4, 'announce_type' => 0, 'groupCount' => 2, 'group1' => '0,9', 'group2' => '3');
        $cp = $this->campaign_helper->createCampaign($this->brand_id, $cp_data);

        $this->assertEquals(2, $cp->getCountCpActionGroups());

        $groups_array = $cp->getCpActionGroups()->toArray();

        $cp_actions_array = $groups_array[0]->getCpActions()->toArray();
        $this->assertEquals(0, $cp_actions_array[0]->type);
        $this->assertEquals(9, $cp_actions_array[1]->type);

        $cp_actions_array = $groups_array[1]->getCpActions()->toArray();
        $this->assertEquals(3, $cp_actions_array[0]->type);

        $this->campaign_helper->cleanupCampaignsByBrandId($this->brand_id);

        $cps = aafwEntityStoreFactory::create('Cps');
        $this->assertEquals(0,$cps->count(array('brand_id' => $cp->brand_id)));
    }

    /**
     * CPユーザ削除
     */
    public function testCleanUpCampaignUsers() {
        $this->campaign_helper->cleanupCampaigns();

        $cp_data = array('cps_type' => 1, 'skeleton_type' => 4, 'announce_type' => 0, 'groupCount' => 2, 'group1' => '0,9', 'group2' => '3');
        $cp = $this->campaign_helper->createCampaign($this->brand_id, $cp_data);

        $this->assertEquals(2, $cp->getCountCpActionGroups());

        $groups_array = $cp->getCpActionGroups()->toArray();

        $cp_actions_array = $groups_array[0]->getCpActions()->toArray();
        $this->assertEquals(0, $cp_actions_array[0]->type);
        $this->assertEquals(9, $cp_actions_array[1]->type);

        $cp_actions_array = $groups_array[1]->getCpActions()->toArray();
        $this->assertEquals(3, $cp_actions_array[0]->type);

        $this->campaign_helper->cleanupCampaignUsers();

        $this->campaign_helper->cleanupCampaigns();

        $cps = aafwEntityStoreFactory::create('Cps');
        $this->assertEquals(0,$cps->count(array('brand_id' => $cp->brand_id)));
    }

    /**
     * CPユーザ削除
     */
    public function testCleanUpCampaignUsersByCpId() {
        $this->campaign_helper->cleanupCampaigns();

        $cp_data = array('cps_type' => 1, 'skeleton_type' => 4, 'announce_type' => 0, 'groupCount' => 2, 'group1' => '0,9', 'group2' => '3');
        $cp = $this->campaign_helper->createCampaign($this->brand_id, $cp_data);

        $this->assertEquals(2, $cp->getCountCpActionGroups());

        $groups_array = $cp->getCpActionGroups()->toArray();

        $cp_actions_array = $groups_array[0]->getCpActions()->toArray();
        $this->assertEquals(0, $cp_actions_array[0]->type);
        $this->assertEquals(9, $cp_actions_array[1]->type);

        $cp_actions_array = $groups_array[1]->getCpActions()->toArray();
        $this->assertEquals(3, $cp_actions_array[0]->type);

        $this->campaign_helper->cleanupCampaignUsers();

        $this->campaign_helper->cleanupCampaigns();

        $cps = aafwEntityStoreFactory::create('Cps');
        $this->assertEquals(0,$cps->count(array('brand_id' => $cp->brand_id)));
    }

}