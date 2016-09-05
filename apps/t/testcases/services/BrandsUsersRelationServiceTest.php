<?php
AAFW::import ('jp.aainc.classes.services.BrandsUsersRelationService');

class BrandsUsersRelationServiceTest extends BaseTest {

    /** @var  BrandsUsersRelationService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("BrandsUsersRelationService");
    }

    public function testCountBrandsUsersRelationsByBrandId01_whenArgIsNull() {
        $this->assertNull($this->target->countBrandsUsersRelationsByBrandId(null));
    }

    public function testCountBrandsUsersRelationsByBrandId02_whenOneUserFoundPattern1() {
        $brand = $this->entity('Brands');
        $this->assertEquals(0, $this->target->countBrandsUsersRelationsByBrandId($brand->id));
    }

    public function testCountBrandsUsersRelationsByBrandId03_whenOneUserFoundPattern1() {
        $brand = $this->entity('Brands');
        $user = $this->newUser();
        $this->entity('BrandsUsersRelations', array('brand_id' => $brand->id, 'user_id' => $user->id));
        $this->assertEquals(1, $this->target->countBrandsUsersRelationsByBrandId($brand->id));
    }

    public function testCountBrandsUsersRelationsByBrandId04_whenOneUserFoundWithOnePattern2() {
        $brand = $this->entity('Brands');
        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $this->entity('BrandsUsersRelations', array('brand_id' => $brand->id, 'user_id' => $user1->id));
        $this->entity('BrandsUsersRelations', array('brand_id' => $brand->id, 'user_id' => $user2->id, 'withdraw_flg' => 1));
        $this->assertEquals(1, $this->target->countBrandsUsersRelationsByBrandId($brand->id));
    }

    public function testCountBrandsUsersRelationsByBrandId05_whenOneUserFoundWithTwoPatter3() {
        $brand = $this->entity('Brands');
        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $this->entity('BrandsUsersRelations', array('brand_id' => $brand->id, 'user_id' => $user1->id));
        $del_target  = $this->entity('BrandsUsersRelations', array('brand_id' => $brand->id, 'user_id' => $user2->id));
        $this->executeQuery("UPDATE brands_users_relations SET del_flg = 1 WHERE id = " . $del_target->id);
        $this->assertEquals(1, $this->target->countBrandsUsersRelationsByBrandId($brand->id));
    }

    public function testCountBrandsUsersRelationsByBrandId06_whenTwoUserFoundWithTwoPattern1() {
        $brand = $this->entity('Brands');
        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();
        $this->entity('BrandsUsersRelations', array('brand_id' => $brand->id, 'user_id' => $user1->id));
        $this->entity('BrandsUsersRelations', array('brand_id' => $brand->id, 'user_id' => $user2->id));
        $this->entity('BrandsUsersRelations', array('brand_id' => $brand->id, 'user_id' => $user3->id, 'withdraw_flg' => 1));
        $this->assertEquals(2, $this->target->countBrandsUsersRelationsByBrandId($brand->id));
    }
}