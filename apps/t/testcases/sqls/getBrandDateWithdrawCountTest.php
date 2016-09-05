<?php
AAFW::import('jp.aainc.classes.services.DashboardService');

class getBrandDateWithdrawCountTest extends BaseTest {

    public function testGetBrandDateRegisterCount01() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1));

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();
        $user4 = $this->newUser();
        $user5 = $this->newUser();
        $user6 = $this->newUser();
        $user7 = $this->newUser();

        $relations = $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00')),
                'withdraw_flg' => 1),
            array('user_id' => $user3->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
            array('user_id' => $user4->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00')),
                'withdraw_flg' => 1),
            array('user_id' => $user5->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00')),
                'withdraw_flg' => 1),
            array('user_id' => $user6->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
            array('user_id' => $user7->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-03 12:00:00')),
                'withdraw_flg' => 1),
        ));

        $this->entities('WithdrawLogs', array(
            array('brand_user_relation_id' => $relations[1]->id,
                'created_at'=> date('Y-m-d H:i:s', strtotime('2015-05-01 13:00:00'))),
            array('brand_user_relation_id' => $relations[3]->id,
                'created_at'=> date('Y-m-d H:i:s', strtotime('2015-05-01 13:00:00'))),
            array('brand_user_relation_id' => $relations[4]->id,
                'created_at'=> date('Y-m-d H:i:s', strtotime('2015-05-02 13:00:00'))),
            array('brand_user_relation_id' => $relations[6]->id,
                'created_at'=> date('Y-m-d H:i:s', strtotime('2015-05-03 13:00:00'))),
        ));

        $condition = array(
            'brand_id' => $brand->id,
            'from_date' => date('Y-m-d H:i:s', strtotime('2015-04-30 00:00:00')),
            'to_date' => date('Y-m-d H:i:s', strtotime('2015-05-02 23:59:59'))
        );
        $order = array(array('name' => 'withdraw_date','direction' => 'ASC'));
        $args = array($condition, $order, '', '', '');

        $result = $this->getBrandDateWithdrawCount($args[0]);

        $expect_result = array(
            array('withdraw_date' => '2015/05/01',
                'cnt' => '2'),
            array('withdraw_date' => '2015/05/02',
                'cnt' => '1'),
        );
        $this->assertEquals($expect_result, $result);
    }
}
