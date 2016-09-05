<?php

class getBrandSnsCountTest extends BaseTest {

    public function testGetBrandSnsCount01() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1));

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();
        $user4 = $this->newUser();
        $user5 = $this->newUser();

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
            array('user_id' => $user5->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
        ));

        $this->entities('SocialAccounts', array(
            array('user_id' => $user1->id,
                'social_media_id' => 1,
                'social_media_account_id' => 1,
                'name'=> 'test1',
                'validated' => 1),
            array('user_id' => $user1->id,
                'social_media_id' => 3,
                'social_media_account_id' => 1,
                'name'=> 'test1',
                'validated' => 1),
            array('user_id' => $user1->id,
                'social_media_id' => 4,
                'social_media_account_id' => 1,
                'name'=> 'test1',
                'validated' => 1),
            array('user_id' => $user2->id,
                'social_media_id' => 1,
                'social_media_account_id' => 1,
                'name'=> 'test2',
                'validated' => 1),
            array('user_id' => $user2->id,
                'social_media_id' => 5,
                'social_media_account_id' => 1,
                'name'=> 'test2',
                'validated' => 1),
            array('user_id' => $user3->id,
                'social_media_id' => 6,
                'social_media_account_id' => 1,
                'name'=> 'test3',
                'validated' => 1),
            array('user_id' => $user4->id,
                'social_media_id' => 1,
                'social_media_account_id' => 1,
                'name'=> 'test4',
                'validated' => 1),
            array('user_id' => $user4->id,
                'social_media_id' => 4,
                'social_media_account_id' => 1,
                'name'=> 'test4',
                'validated' => 1),
            array('user_id' => $user4->id,
                'social_media_id' => 5,
                'social_media_account_id' => 1,
                'name'=> 'test4',
                'validated' => 1),
        ));

        $condition = array(
            'brand_id' => $brand->id,
            'from_date' => date('Y-m-d H:i:s', strtotime('2015-04-30 00:00:00')),
            'to_date' => date('Y-m-d H:i:s', strtotime('2015-05-02 23:59:59')),
            'social_media_gdo' => '__ON__'
        );
        $order = array(array('name' => 'tmp.social_media_id','direction' => 'ASC'));
        $args = array($condition, $order, '', '', '');

        $result = $this->getBrandSnsFanCount($args[0]);

        $expect_result = array(
            array('social_media_id' => '-1',
                'cnt' => 1),
            array('social_media_id' => '1',
                'cnt' => 2),
            array('social_media_id' => '3',
                'cnt' => 1),
            array('social_media_id' => '4',
                'cnt' => 1),
            array('social_media_id' => '5',
                'cnt' => 1),
            array('social_media_id' => '6',
                'cnt' => 1),
        );
        $this->assertEquals($expect_result, $result);
    }
}
