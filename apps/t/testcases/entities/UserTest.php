<?php

AAFW::import ('jp.aainc.aafw.classes.entities.User');

class UserTest extends BaseTest {

    public function test_getBrandsUsersRelations() {
        $relations = $this->newBrandToBrandUsersRelation();
        $brand_users_relations = $relations[1]->getBrandsUsersRelations();
        $this->assertEquals($relations[2]->user_id, $brand_users_relations->toArray()[0]->user_id);
    }

    public function test_getBrandsUsersRelation() {
        $relations = $this->newBrandToBrandUsersRelation();
        $brand_users_relation = $relations[1]->getBrandsUsersRelation();
        $this->assertEquals($relations[2]->user_id, $brand_users_relation->user_id);
    }
}