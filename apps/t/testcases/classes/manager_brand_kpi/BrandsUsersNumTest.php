<?php

AAFW::import('jp.aainc.classes.manager_brand_kpi.BrandsUsersNum');

class BrandsUsersNumTest extends BaseTest {

    protected function setUp() {
        $this->clearBrandAndRelatedEntities();
    }
    public function testDoExecute01_whenBrandIdSpecifiedAndNoData() {
        $target = new BrandsUsersNum();
        $count = $target->doExecute(date('Y-m-d'), -1);
        $this->assertEquals(0, $count);
    }

    public function testDoExecute02_whenBrandIdSpecifiedAndOneRecordFound() {
        $brand1 = $this->entity('Brands');
        $brand2 = $this->entity('Brands');
        $user1 = $this->newUser();
        $user2 = $this->newUser();

        $this->entity('BrandsUsersRelations', array(
            'brand_id' => $brand1->id,
            'user_id' => $user1->id
        ));

        $this->entity('BrandsUsersRelations', array(
            'brand_id' => $brand2->id,
            'user_id' => $user2->id
        ));

        $target = new BrandsUsersNum();
        $count = $target->doExecute(date('Y-m-d'), $brand1->id);
        $this->assertEquals(1, $count);
    }

    public function testDoExecute03_whenBrandIdSpecifiedAndOneRecordFoundVerTwo() {
        $brand1 = $this->entity('Brands');
        $user1 = $this->newUser();
        $user2 = $this->newUser();

        $this->entity('BrandsUsersRelations', array(
            'brand_id' => $brand1->id,
            'user_id' => $user1->id
        ));

        $date_base = date('Y-m-d');
        $future_date = date('Y-m-d', strtotime($date_base .' +1 day'));
        $this->entity('BrandsUsersRelations', array(
            'brand_id' => $brand1->id,
            'user_id' => $user2->id,
            'created_at' => $future_date
        ));

        $target = new BrandsUsersNum();
        $count = $target->doExecute($date_base, $brand1->id);
        $this->assertEquals(1, $count);
    }

    public function testDoExecute04_whenBrandIdSpecifiedAndTwoRecordFound() {
        $brand1 = $this->entity('Brands');
        $user1 = $this->newUser();
        $user2 = $this->newUser();

        $this->entity('BrandsUsersRelations', array(
            'brand_id' => $brand1->id,
            'user_id' => $user1->id
        ));

        $this->entity('BrandsUsersRelations', array(
            'brand_id' => $brand1->id,
            'user_id' => $user2->id
        ));

        $target = new BrandsUsersNum();
        $count = $target->doExecute(date('Y-m-d'), $brand1->id);
        $this->assertEquals(2, $count);
    }

    public function testDoExecute05_whenOneRecordFound() {
        $brand1 = $this->entity('Brands');
        $user1 = $this->newUser();
        $this->entity('BrandsUsersRelations', array(
            'brand_id' => $brand1->id,
            'user_id' => $user1->id
        ));

        $target = new BrandsUsersNum();
        $count = $target->doExecute(date('Y-m-d'));
        $this->assertEquals(1, $count);
    }
}