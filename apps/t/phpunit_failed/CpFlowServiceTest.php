<?php
require_once __DIR__ . '/../../../../config/define.php';
AAFW::import('jp.aainc.classes.services.CpFlowService');

class CpFlowServiceTest extends PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function Cpの作成() {
        $brand_id = 1;
        $cp_id = 1;
        $service = new CpFlowService();
        $cp = $service->createCp($brand_id);
        $this->assertEquals($brand_id, $cp->brand_id);
        $this->assertEquals($cp_id, $cp->id);
    }

    /**
     *
     */
    public function Cpの取得() {
        $brand_id = 1;
        $cp_id = 1;
        $service = new CpFlowService();
        $cp = $service->getCpById($cp_id);
        $this->assertEquals($brand_id, $cp->brand_id);
    }

    /**
     *
     */
    public function Cpの更新() {
        $title = "test";
        $cp_id = 1;
        $service = new CpFlowService();
        $cp = $service->getCpById($cp_id);
        $cp->title = $title;
        $service->updateCp($cp);
        $cp = $service->getCpById($cp_id);
        $this->assertEquals($title, $cp->title);
    }

    /**
     *
     */
    public function Cpの削除() {
        $cp_id = 1;
        $service = new CpFlowService();
        $cp = $service->getCpById($cp_id);
        $service->deleteCp($cp);
        $cp = $service->getCpById($cp_id);
        $this->assertNull($cp);
    }

    /**
     *
     */
    public function Cpの公開予約() {
        $cp_id = 1;
        $status = Cp::STATUS_SCHEDULE;
        $service = new CpFlowService();
        $service->scheduleCp($cp_id);
        $cp = $service->getCpById($cp_id);
        $this->assertEquals($status, $cp->status);
    }

    /**
     *
     */
    public function Cpの公開予約の取り消し() {
        $cp_id = 1;
        $status = Cp::STATUS_DRAFT;
        $service = new CpFlowService();
        $service->cancelScheduleCp($cp_id);
        $cp = $service->getCpById($cp_id);
        $this->assertEquals($status, $cp->status);
    }

    /**
     *
     */
    public function Cpの予約状態を確認() {

        $cp_id = 1;
        $service = new CpFlowService();
        $result = $service->isScheduledCp($cp_id);
        $this->assertFalse($result);

        $service->scheduleCp($cp_id);
        $result = $service->isScheduledCp($cp_id);
        $this->assertTrue($result);

        $service->cancelScheduleCp($cp_id);
        $result = $service->isScheduledCp($cp_id);
        $this->assertFalse($result);

    }

    /**
     *
     */
    public function CpActionGroupの作成() {

        $cp_action_group_id = 1;
        $cp_id = 1;
        $order_no = 1;

        $service = new CpFlowService();
        $cp_action_group = $service->createCpActionGroup($cp_id, $order_no);

        $this->assertEquals($order_no, $cp_action_group->order_no);
        $this->assertEquals($cp_id, $cp_action_group->cp_id);
        $this->assertEquals($cp_action_group_id, $cp_action_group->id);
    }

    /**
     *
     */
    public function CpActionGroupの取得() {

        $cp_action_group_id = 1;
        $cp_id = 1;
        $order_no = 1;

        $service = new CpFlowService();
        $cp_action_group = $service->getCpActionGroupById($cp_action_group_id);

        $this->assertEquals($order_no, $cp_action_group->order_no);
        $this->assertEquals($cp_id, $cp_action_group->cp_id);
        $this->assertEquals($cp_action_group_id, $cp_action_group->id);
    }

    /**
     *
     */
    public function CpIdを利用してCpActionGroupsを取得() {

        $cp_action_group_id = 1;
        $cp_id = 1;
        $order_no = 1;

        $service = new CpFlowService();
        $cp_action_groups = $service->getCpActionGroupsByCpId($cp_id);

        if (count($cp_action_groups) > 0) {

            foreach ($cp_action_groups as $cp_action_group) {
                $this->assertEquals($order_no, $cp_action_group->order_no);
                $this->assertEquals($cp_id, $cp_action_group->cp_id);
                $this->assertEquals($cp_action_group_id, $cp_action_group->id);
            }

        }
    }

    /**
     *
     */
    public function CpActionGroupの更新() {

        $cp_action_group_id = 1;
        $order_no = 2;
        $service = new CpFlowService();
        $cp_action_group = $service->getCpActionGroupById($cp_action_group_id);
        $cp_action_group->order_no = 2;
        $service->updateCpActionGroup($cp_action_group);
        $cp_action_group = $service->getCpActionGroupById($cp_action_group_id);
        $this->assertEquals($cp_action_group_id, $cp_action_group->id);
        $this->assertEquals($order_no, $cp_action_group->order_no);
    }

    /**
     *
     */
    public function CpActionGroupの削除() {

        $cp_action_group_id = 1;
        $service = new CpFlowService();
        $cp_action_group = $service->getCpActionGroupById($cp_action_group_id);
        $service->deleteCpActionGroup($cp_action_group);
        $cp_action_group = $service->getCpActionGroupById($cp_action_group_id);
        $this->assertNull($cp_action_group);
    }

    /**
     * @test
     */
    public function CpActionの作成() {

        $cp_action_group_id = 1;
        $cp_action_id = 1;
        $order_no = 1;

        $service = new CpFlowService();
        $cp_action = $service->createCpAction($cp_action_group_id, CpAction::TYPE_ENTRY, CpAction::STATUS_DRAFT, $order_no);

        $this->assertEquals($order_no, $cp_action->order_no);
        $this->assertEquals($cp_action_id, $cp_action->id);
        $this->assertEquals($cp_action_group_id, $cp_action->cp_action_group_id);
        $this->assertEquals(CpAction::STATUS_DRAFT, $cp_action->status);
        $this->assertEquals(CpAction::TYPE_ENTRY, $cp_action->type);
        $this->assertEquals($order_no, $cp_action->order_no);
    }

    /**
     *
     */
    public function CpActionの取得() {

        $cp_action_group_id = 1;
        $cp_action_id = 1;
        $order_no = 1;

        $service = new CpFlowService();
        $cp_action = $service->getCpActionById($cp_action_id);

        $this->assertEquals($order_no, $cp_action->order_no);
        $this->assertEquals($cp_action_id, $cp_action->id);
        $this->assertEquals($cp_action_group_id, $cp_action->cp_action_group_id);
        $this->assertEquals(CpAction::STATUS_DRAFT, $cp_action->status);
        $this->assertEquals(CpAction::TYPE_ENTRY, $cp_action->type);
        $this->assertEquals($order_no, $cp_action->order_no);
    }


    /**
     *
     */
    public function CpActionGroupIdを利用してCpActionsを取得() {

        $cp_action_group_id = 1;
        $cp_action_id = 1;
        $order_no = 1;

        $service = new CpFlowService();
        $cp_actions = $service->getCpActionsByCpActionGroupId($cp_action_group_id);

        if (count($cp_actions) > 0) {

            foreach ($cp_actions as $cp_action) {
                $this->assertEquals($order_no, $cp_action->order_no);
                $this->assertEquals($cp_action_id, $cp_action->id);
                $this->assertEquals($cp_action_group_id, $cp_action->cp_action_group_id);
                $this->assertEquals(CpAction::STATUS_DRAFT, $cp_action->status);
                $this->assertEquals(CpAction::TYPE_ENTRY, $cp_action->type);
                $this->assertEquals($order_no, $cp_action->order_no);
            }
        }
    }

    /**
     *
     */
    public function CpActionの更新() {

        $cp_action_group_id = 1;
        $cp_action_id = 1;
        $order_no = 2;

        $service = new CpFlowService();
        $cp_action = $service->getCpActionById($cp_action_id);

        $cp_action->order_no = 2;
        $cp_action->status = CpAction::STATUS_FIX;
        $cp_action->type = CpAction::TYPE_ANNOUNCE;
        $service->updateCpAction($cp_action);

        $cp_action = $service->getCpActionById($cp_action_id);

        $this->assertEquals($order_no, $cp_action->order_no);
        $this->assertEquals($cp_action_id, $cp_action->id);
        $this->assertEquals($cp_action_group_id, $cp_action->cp_action_group_id);
        $this->assertEquals(CpAction::STATUS_FIX, $cp_action->status);
        $this->assertEquals(CpAction::TYPE_ANNOUNCE, $cp_action->type);
        $this->assertEquals($order_no, $cp_action->order_no);
    }


    /**
     *
     */
    public function isFixedCpActionsの確認() {

        $cp_action_group_id = 1;
        $cp_action_id = 1;

        $service = new CpFlowService();
        $cp_action = $service->getCpActionById($cp_action_id);

        $cp_action->order_no = 1;
        $cp_action->status = CpAction::STATUS_DRAFT;
        $cp_action->type = CpAction::TYPE_MESSAGE;
        $service->updateCpAction($cp_action);

        $result = $service->isFixedCpActions($cp_action_group_id);
        $this->assertFalse($result);

        $cp_action->status = CpAction::STATUS_FIX;
        $service->updateCpAction($cp_action);

        $result = $service->isFixedCpActions($cp_action_group_id);
        $this->assertTrue($result);
    }


    /**
     *
     */
    public function isFixedCpInfoの確認() {

        $cp_id = 1;

        $service = new CpFlowService();
        $cp = $service->getCpById($cp_id);

        $cp->fix_basic_flg = 0;
        $cp->fix_attract_flg = 0;
        $service->updateCp($cp);

        $result = $service->isFixedCpInfo($cp_id);
        $this->assertFalse($result);

        $cp->fix_basic_flg = 0;
        $cp->fix_attract_flg = 1;
        $service->updateCp($cp);

        $result = $service->isFixedCpInfo($cp_id);
        $this->assertFalse($result);

        $cp->fix_basic_flg = 1;
        $cp->fix_attract_flg = 0;
        $service->updateCp($cp);

        $result = $service->isFixedCpInfo($cp_id);
        $this->assertFalse($result);

        $cp->fix_basic_flg = 1;
        $cp->fix_attract_flg = 1;
        $service->updateCp($cp);

        $result = $service->isFixedCpInfo($cp_id);
        $this->assertTrue($result);
    }

    /**
     *
     */
    public function isCanPublishの確認() {
        $cp_id = 1;
        $cp_action_id = 1;

        $service = new CpFlowService();
        $cp = $service->getCpById($cp_id);

        $cp->fix_basic_flg = 0;
        $cp->fix_attract_flg = 0;
        $service->updateCp($cp);

        $cp_action = $service->getCpActionById($cp_action_id);
        $cp_action->status = CpAction::STATUS_DRAFT;
        $service->updateCpAction($cp_action);

        $result = $service->canPublicCp($cp_id);
        $this->assertFalse($result);

        $cp_action = $service->getCpActionById($cp_action_id);
        $cp_action->status = CpAction::STATUS_FIX;
        $service->updateCpAction($cp_action);

        $result = $service->canPublicCp($cp_id);
        $this->assertFalse($result);

        $cp->fix_basic_flg = 0;
        $cp->fix_attract_flg = 1;
        $service->updateCp($cp);

        $cp_action = $service->getCpActionById($cp_action_id);
        $cp_action->status = CpAction::STATUS_DRAFT;
        $service->updateCpAction($cp_action);

        $result = $service->canPublicCp($cp_id);
        $this->assertFalse($result);

        $cp_action = $service->getCpActionById($cp_action_id);
        $cp_action->status = CpAction::STATUS_FIX;
        $service->updateCpAction($cp_action);

        $result = $service->canPublicCp($cp_id);
        $this->assertFalse($result);

        $cp->fix_basic_flg = 1;
        $cp->fix_attract_flg = 0;
        $service->updateCp($cp);

        $cp_action = $service->getCpActionById($cp_action_id);
        $cp_action->status = CpAction::STATUS_DRAFT;
        $service->updateCpAction($cp_action);

        $result = $service->canPublicCp($cp_id);
        $this->assertFalse($result);

        $cp_action = $service->getCpActionById($cp_action_id);
        $cp_action->status = CpAction::STATUS_FIX;
        $service->updateCpAction($cp_action);

        $result = $service->canPublicCp($cp_id);
        $this->assertFalse($result);

        $cp->fix_basic_flg = 1;
        $cp->fix_attract_flg = 1;
        $service->updateCp($cp);

        $cp_action = $service->getCpActionById($cp_action_id);
        $cp_action->status = CpAction::STATUS_DRAFT;
        $service->updateCpAction($cp_action);

        $result = $service->canPublicCp($cp_id);
        $this->assertFalse($result);

        $cp_action = $service->getCpActionById($cp_action_id);
        $cp_action->status = CpAction::STATUS_FIX;
        $service->updateCpAction($cp_action);

        $result = $service->canPublicCp($cp_id);
        $this->assertTrue($result);

    }

    /**
     *
     */
    public function CpActionの削除() {

        $cp_action_id = 1;
        $service = new CpFlowService();
        $cp_action = $service->getCpActionById($cp_action_id);
        $service->deleteCpAction($cp_action);
        $cp_action = $service->getCpActionById($cp_action_id);
        $this->assertNull($cp_action);
    }

    /**
     *
     */
    public function CpNextActionの作成() {

        $cp_next_action_id = 2;
        $cp_action_id = 1;

        $cp_action_group_id = 1;
        $service = new CpFlowService();
        $first_cp_action = $service->createCpAction($cp_action_group_id, CpAction::TYPE_ENTRY, CpAction::STATUS_DRAFT, 1);
        $next_cp_action = $service->createCpAction($cp_action_group_id, CpAction::TYPE_MESSAGE, CpAction::STATUS_DRAFT, 2);
        $cp_next_action = $service->createCpNextAction($first_cp_action->id, $next_cp_action->id);

        $this->assertEquals($cp_action_id, $cp_next_action->cp_action_id);
        $this->assertEquals($cp_next_action_id, $cp_next_action->cp_next_action_id);

    }

    /**
     *
     */
    public function CpNextActionの取得() {

        $id = 1;
        $cp_next_action_id = 2;
        $cp_action_id = 1;

        $service = new CpFlowService();
        $cp_next_action = $service->getCpNextActionById($id);
        $this->assertEquals($cp_next_action_id, $cp_next_action->cp_next_action_id);
        $this->assertEquals($cp_action_id, $cp_next_action->cp_action_id);

    }

    /**
     *
     */
    public function CpNextActionの更新() {

        $id = 1;
        $cp_next_action_id = 2;
        $cp_action_id = 1;

        $service = new CpFlowService();
        $cp_next_action = $service->getCpNextActionById($id);
        $cp_next_action->cp_action_id = $cp_next_action_id;
        $cp_next_action->cp_next_action_id = $cp_action_id;
        $service->updateCpNextAction($cp_next_action);


        $this->assertEquals($cp_next_action_id, $cp_next_action->cp_action_id);
        $this->assertEquals($cp_action_id, $cp_next_action->cp_next_action_id);

    }

    /**
     *
     */
    public function CpNextActionの削除() {

        $id = 1;
        $service = new CpFlowService();
        $cp_next_action = $service->getCpNextActionById($id);
        $service->deleteCpNextAction($cp_next_action);
        $cp_next_action = $service->getCpNextActionById($id);
        $this->assertNull($cp_next_action);
    }
}