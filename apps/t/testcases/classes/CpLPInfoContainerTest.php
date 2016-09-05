<?php

AAFW::import('jp.aainc.classes.CpLPInfoContainer');
AAFW::import('jp.aainc.classes.CacheManager');

class CpLPInfoContainerTest extends BaseTest {

    protected function setUp() {
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
        $this->setPrivateFieldValue(new CacheManager(), "connection", new CacheManager_Connection());
        $this->clearBrandAndRelatedEntities();
    }
//
//    public function testGetCpLPInfo01_ifNoCache() {
//        $brand = $this->entity('Brands');
//        $brand->name = 'TEST';
//        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
//        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 1));
//        $cp_action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'order_no' => 1));
//        $this->entity('CpEntryActions', array('cp_action_id' => $cp_action->id));
//
//        $target = new CpLPInfoContainer();
//
//        $info = $target->getCpLPInfo($cp, $brand);
//
//        $this->assertEquals(
//            array(
//                '{"ea":{},"ai":{"cp":{"id":"' . $cp->id . '","created_at":null,"sponsor":"TEST","can_entry":false,' .
//                '"url":"https:\/\/brandcotest.com\/\/campaigns\/' . $cp->id . '","shipping_method":null,"winner_count":null,' .
//                '"show_winner_label":null,"winner_label":null,"show_recruitment_note":null,"recruitment_note":null,' .
//                '"join_limit_sns_flg":null,"join_limit_flg":null,"share_flg":null,"join_limit_sns":[],' .
//                '"join_limit_sns_without_platform":false,"extend_tag":"","start_date":"1970\/01\/01\uff08\u6728\uff09","start_datetime":"1970\/01\/01 (\u6728) 09:00",' .
//                '"end_date":"1970\/01\/01\uff08\u6728\uff09","end_datetime":"1970\/01\/01 (\u6728) 09:00","announce_date":"1970\/01\/01\uff08\u6728\uff09",' .
//                '"is_au_campaign":false,"au_login_url":"http:\/\/pass.auone.jp\/gate\/?nm=1&ru=http%3A%2F%2Fbrandcotest.com%2Fmy%2Fsignup%3Fcp_id%3D' . $cp->id . '","announce_display_label_use_flg":null,"announce_display_label":null,"is_permanent_cp":false,"is_non_incentive":false},"' .
//                'concrete_action":{"text":"","html_content":"","image_url":"","button_label_text":"\u5fdc\u52df\u3059\u308b"}},' .
//                '"oi":{"title":"\u540d\u79f0\u672a\u8a2d\u5b9a\u306e\u30ad\u30e3\u30f3\u30da\u30fc\u30f3 \/ ","image":null,' .
//                '"description":"","url":"https:\/\/brandcotest.com\/\/campaigns\/' . $cp->id . '","brand_image":null,"brand_name":"TEST"}}',
//                aafwRedisManager::getRedisInstance()->exists('cache:cli:' . $cp->id)
//            ),
//            array(json_encode($info), true));
//    }

    public function testGetCpLPInfo02_ifOnCacheAndBrandInfoIsDifferedFromCache() {
        $brand = $this->entity('Brands');
        $brand->name = 'TEST';
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => 1));
        $cp_action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'order_no' => 1));
        $this->entity('CpEntryActions', array('cp_action_id' => $cp_action->id));

        $target = new CpLPInfoContainer();

        $target->getCpLPInfo($cp, $brand);
        $info = $target->getCpLPInfo($cp, $brand);

        $this->assertEquals(
            array(
                '{"ea":{},"ai":{"cp":{"id":"' . $cp->id . '","created_at":null,"sponsor":"TEST","can_entry":false,' .
                '"url":"https:\/\/brandcotest.com\/\/campaigns\/' . $cp->id . '","shipping_method":null,"winner_count":null,' .
                '"show_winner_label":null,"winner_label":null,"show_recruitment_note":null,"recruitment_note":null,' .
                '"join_limit_sns_flg":null,"join_limit_flg":null,"share_flg":null,"join_limit_sns":[],' .
                '"join_limit_sns_without_platform":false,"extend_tag":"","start_date":"1970\/01\/01\uff08\u6728\uff09","start_datetime":"1970\/01\/01 (\u6728) 09:00",' .
                '"end_date":"1970\/01\/01\uff08\u6728\uff09","end_datetime":"1970\/01\/01 (\u6728) 09:00","announce_date":"1970\/01\/01\uff08\u6728\uff09",' .
                '"is_au_campaign":false,"au_login_url":"http:\/\/pass.auone.jp\/gate\/?nm=1&ru=http%3A%2F%2Fbrandcotest.com%2Fmy%2Fsignup%3Fcp_id%3D' . $cp->id .
                '","announce_display_label_use_flg":null,"announce_display_label":null,"is_permanent_cp":false,"is_non_incentive":false},"concrete_action":{"text":"","html_content":"","image_url":"","button_label_text":"\u5fdc\u52df\u3059\u308b"}},' .
                '"oi":{"title":"\u540d\u79f0\u672a\u8a2d\u5b9a\u306e\u30ad\u30e3\u30f3\u30da\u30fc\u30f3 \/ TEST","image":null,"description":"",' .
                '"url":"https:\/\/brandcotest.com\/\/campaigns\/' . $cp->id . '","brand_image":null,"brand_name":"TEST"}}',
                aafwRedisManager::getRedisInstance()->exists('cache:cli:' . $cp->id)
            ),
            array(json_encode($info), true));
    }
}
