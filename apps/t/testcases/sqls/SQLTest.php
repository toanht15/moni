<?php


class SQLTest extends BaseTest {

    public function testGetActiveUsersInAMonth() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1));
        $this->truncateAll('LoginLogDatas');
        $this->entities('LoginLogDatas', array(
            array('id' => '1',
                'user_id'=> '1',
                'brand_id'=> $brand->id,
                'login_date'=> '2015-05-25 11:52:30'),
            array('id' => '2',
                'user_id'=> '1',
                'brand_id'=> $brand->id,
                'login_date'=> '2015-04-25 11:52:30')
        ));

        $result = $this->getActiveUsersInAMonth(array(
            'this_year' => 2015,
            'this_month' => 5
        ));

        $active_user_count = array('users' => '1');
        $this->assertEquals(json_decode($active_user_count), json_decode($result));
    }
}