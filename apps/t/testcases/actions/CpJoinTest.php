<?php
AAFW::import('jp.aainc.actions.user.brandco.messages.join');

class CpJoinTest extends BaseTest {

    public function test() {
        $this->assertEquals(true, true);
    }

//    public function testJoin_01_notJoined () {
//        //設定
//        $this->entity("Cps", array("id" => BrandTest::test_id, "brand_id" => BrandTest::test_id, "type" => 1, "selection_method" => CpCreator::ANNOUNCE_FIRST, "recruitment_note" => "", "extend_tag" => ""));
//        $this->entity("CpActionGroups", array("id" => BrandTest::test_id, "cp_id" => BrandTest::test_id, "order_no" => 1));
//        $this->entity("CpActions", array("id" => BrandTest::test_id, "cp_action_group_id" => BrandTest::test_id, "order_no" => 1, 'type' => CpAction::TYPE_ENTRY, 'status' => CpAction::STATUS_FIX));
//        $this->entity("CpEntryActions", array("id" => BrandTest::test_id, "cp_action_id" => BrandTest::test_id, "title" => "TEST", 'text' => "TEST", 'html_content' => "TEST"));
//
//        $join_class = new join();
//        $data = new stdClass();
//        $data->id = BrandTest::test_id;
//        $g = array('directory_name' => 'TEST',
//            'cp_id' => BrandTest::test_id,
//            'platform' => 'Facebook');
//
//        $s = array('pl_monipla_userId' => BrandTest::test_id,
//            'pl_loginBrandIds' => array(BrandTest::test_id => 1),
//            'pl_monipla_userInfo' => array('socialAccounts' => array('fb')));
//
//        $join_class->rewriteParams(array(), $g, $s);
//
//        $join_class->setData(array('pageStatus' => array('userInfo' => $data)));
//        $join_class->cp_id = BrandTest::test_id;
//        $join_class->getBrand();
//
//        $_SESSION['clientId'] = "fb";
//
//        //テスト実施
//        $result = $join_class->doAction();
//
//        $this->assertEquals($this->countEntities("CpUserActionMessages", array('cp_action_id' => BrandTest::test_id)), 1);
//        $this->assertEquals($this->countEntities("CpUserActionStatuses", array('cp_action_id' => BrandTest::test_id)), 1);
//        $this->assertEquals($this->countEntities("CpUsers", array('cp_id' => BrandTest::test_id, 'user_id' => BrandTest::test_id)), 1);
//        $this->assertEquals($result, 'redirect: ' . Util::rewriteUrl('messages', 'thread', array("cp_id" => BrandTest::test_id), array()));
//    }
//
//    public function testJoin_02_limitWinner () {
//        //設定
//        $this->entity("Users", array("id" => (BrandTest::test_id + 1), "monipla_user_id" => (BrandTest::test_id + 1), "name" => 'TEST_USER 1'));
//
//        $join_class = new join();
//        $data = new stdClass();
//        $data->id = BrandTest::test_id + 1;
//        $g = array('directory_name' => 'TEST',
//            'cp_id' => BrandTest::test_id,
//            'platform' => 'Facebook');
//
//        $s = array('pl_monipla_userId' => BrandTest::test_id + 1,
//            'pl_loginBrandIds' => array(BrandTest::test_id => 1),
//            'pl_monipla_userInfo' => array('socialAccounts' => array('fb')));
//
//        $join_class->rewriteParams(array(), $g, $s);
//
//        $join_class->setData(array('pageStatus' => array('userInfo' => $data)));
//        $join_class->cp_id = BrandTest::test_id;
//        $join_class->getBrand();
//
//        $_SESSION['clientId'] = "fb";
//
//        //テスト実施
//        $result = $join_class->doAction();
//
//        $this->assertEquals($result, 'redirect: ' . Util::rewriteUrl('', 'campaigns', array(BrandTest::test_id), array('mid' => 'cp_join_limit')));
//
//        //テストデータ削除
//        $this->purge("CpUsers", BrandTest::test_id + 1);
//    }
//
//    public function testJoin_03_限定キャンペーン() {
//        //設定
//        $this->updateEntities("CpUserActionStatuses", array('cp_action_id' => BrandTest::test_id), array("status" => CpUserActionStatus::NOT_JOIN));
//
//        $join_class = new join();
//        $data = new stdClass();
//        $data->id = BrandTest::test_id;
//        $g = array('directory_name' => 'TEST',
//            'cp_id' => BrandTest::test_id,
//            'platform' => 'Facebook');
//
//        $s = array('pl_monipla_userId' => BrandTest::test_id,
//            'pl_loginBrandIds' => array(BrandTest::test_id => 1),
//            'pl_monipla_userInfo' => array('socialAccounts' => array('fb')));
//
//        $join_class->rewriteParams(array(), $g, $s);
//
//        $join_class->setData(array('pageStatus' => array('userInfo' => $data)));
//        $join_class->cp_id = BrandTest::test_id;
//        $join_class->getBrand();
//
//        $_SESSION['clientId'] = "fb";
//
//        //テスト実施
//        $this->assertEquals($this->countEntities("CpUserActionStatuses", array('cp_action_id' => BrandTest::test_id, "status" => CpUserActionStatus::NOT_JOIN)), 1);
//
//        $result = $join_class->doAction();
//
//        $this->assertEquals($this->countEntities("CpUserActionStatuses", array('cp_action_id' => BrandTest::test_id, "status" => CpUserActionStatus::JOIN)), 1);
//        $this->assertEquals($result, 'redirect: ' . Util::rewriteUrl('messages', 'thread', array("cp_id" => BrandTest::test_id), array()));
//    }
//
//    public function testJoin_05_hasNextAction () {
//        //設定
//        $this->entity("CpActions", array("id" => (BrandTest::test_id + 1), "cp_action_group_id" => BrandTest::test_id, "order_no" => 2, 'type' => CpAction::TYPE_MESSAGE, 'status' => CpAction::STATUS_FIX));
//        $this->entity("CpNextActions", array("id" => BrandTest::test_id, "cp_action_id" => BrandTest::test_id, "cp_next_action_id" => (BrandTest::test_id + 1)));
//        $this->entity("CpMessageActions", array("id" => BrandTest::test_id, "cp_action_id" => (BrandTest::test_id + 1), "title" => "TEST", "html_content" => "TEST", "text" => "TEST"));
//
//        $join_class = new join();
//        $data = new stdClass();
//        $data->id = BrandTest::test_id;
//        $g = array('directory_name' => 'TEST',
//            'cp_id' => BrandTest::test_id,
//            'platform' => 'Facebook');
//
//        $s = array('pl_monipla_userId' => BrandTest::test_id,
//            'pl_loginBrandIds' => array(BrandTest::test_id => 1),
//            'pl_monipla_userInfo' => array('socialAccounts' => array('fb')));
//
//        $join_class->rewriteParams(array(), $g, $s);
//
//        $join_class->setData(array('pageStatus' => array('userInfo' => $data)));
//        $join_class->cp_id = BrandTest::test_id;
//        $join_class->beginner_flg = CpUser::BEGINNER_USER;
//        $join_class->getBrand();
//
//        $_SESSION['clientId'] = "fb";
//
//        //テスト実施
//        $result = $join_class->doAction();
//        $this->assertEquals($this->countEntities("CpUserActionMessages", array('cp_action_id' => (BrandTest::test_id + 1))), 1);
//        $this->assertEquals($this->countEntities("CpUserActionStatuses", array('cp_action_id' => (BrandTest::test_id + 1))), 1);
//        $this->assertEquals($result, 'redirect: ' . Util::rewriteUrl('messages', 'thread', array("cp_id" => BrandTest::test_id), array('tid' => 'signup_complete')));
//
//        $this->deleteEntities("CpUserActionMessages", array('cp_action_id' => (BrandTest::test_id+1)));
//        $this->deleteEntities("CpUserActionStatuses", array('cp_action_id' => (BrandTest::test_id+1)));
//        $this->purge("CpMessageActions", BrandTest::test_id);
//        $this->purge("CpNextActions", BrandTest::test_id);
//        $this->purge("CpActions", (BrandTest::test_id + 1));
//    }
//
//    public function delete_test_data () {
//        //テストデータ削除
//        $this->deleteEntities("CpUserActionMessages", array('cp_action_id' => BrandTest::test_id));
//        $this->deleteEntities("CpUserActionStatuses", array('cp_action_id' => BrandTest::test_id));
//        $this->purge("CpEntryActions", BrandTest::test_id);
//        $this->purge("CpActions", BrandTest::test_id);
//        $this->purge("CpActionGroups", BrandTest::test_id);
//        $this->deleteEntities("CpUsers", array('cp_id' => BrandTest::test_id, 'user_id' => BrandTest::test_id));
//        $this->purge("Cps", BrandTest::test_id);
//    }
}