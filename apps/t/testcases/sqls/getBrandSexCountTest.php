<?php

class getBrandSexCountTest extends BaseTest {

    public function testGetBrandSexCount01() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1));

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();
        $user4 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
            array('user_id' => $user3->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
            array('user_id' => $user4->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-03 12:00:00'))),
        ));

        $this->entities('UserSearchInfos', array(
            array('user_id' => $user1->id,
                'sex' => 'm'),
            array('user_id' => $user2->id,
                'sex' => 'f'),
            array('user_id' => $user3->id,
                'sex' => ''),
            array('user_id' => $user4->id,
                'sex' => 'm'),
        ));

        $condition = array(
            'brand_id' => $brand->id,
            'from_date' => date('Y-m-d H:i:s', strtotime('2015-04-30 00:00:00')),
            'to_date' => date('Y-m-d H:i:s', strtotime('2015-05-02 23:59:59')),
        );
        $order = array(array('name' => 'I.sex','direction' => 'ASC'));
        $args = array($condition, $order, '', '', '');

        $result = $this->getBrandSexFanCount($args[0]);

        $expect_result = array(
            array('sex' => '',
                'cnt' => 1),
            array('sex' => 'f',
                'cnt' => 1),
            array('sex' => 'm',
                'cnt' => 1),
        );
        $this->assertEquals($expect_result, $result);
    }
}
