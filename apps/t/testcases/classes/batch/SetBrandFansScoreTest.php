<?php

AAFW::import('jp.aainc.classes.batch.SetBrandFansScore');

/**
 * UTというか、実質的に結合試験。
 *
 * Class SetBrandFansScoreTest
 */
class SetBrandFansScoreTest extends BaseTest {

    public function setup() {
        $this->clearBrandAndRelatedEntities();
    }

    public function testExecuteProcess01_whenNoInputs() {
        $target = new SetBrandFansScore();
        $target->doProcess();
        $this->assertEquals(0, $target->getExecuteCount());
    }

    public function testExecuteProcess02_whenOneInputAndOutput() {
        $target = new SetBrandFansScore();
        list($brand, $user, $brand_users_relation) = $this->newBrandToBrandUsersRelation();
        $this->executeQuery("UPDATE brands_users_relations SET login_count = 1 WHERE id = " . $brand_users_relation->id);
        $target->doProcess();

        $updated_brands_users_relation = $this->findOne('BrandsUsersRelations', array('id' => $brand_users_relation->id));
        $this->assertEquals(
            array("count" => 1, "score" => "10"),
            array("count" => $target->getExecuteCount(), "score" => $updated_brands_users_relation->score));
    }

    public function testExecuteProcess03_whenTwoInputAndOutput() {
        $target = new SetBrandFansScore();

        list($brand1, $user1, $brand_users_relation1) = $this->newBrandToBrandUsersRelation();
        $this->executeQuery("UPDATE brands_users_relations SET login_count = 1 WHERE id = " . $brand_users_relation1->id);

        list($brand2, $user2, $brand_users_relation2) = $this->newBrandToBrandUsersRelation();
        $this->executeQuery("UPDATE brands_users_relations SET login_count = 2 WHERE id = " . $brand_users_relation2->id);

        $target->doProcess();

        $updated_brands_users_relation1 = $this->findOne('BrandsUsersRelations', array('id' => $brand_users_relation1->id));
        $updated_brands_users_relation2 = $this->findOne('BrandsUsersRelations', array('id' => $brand_users_relation2->id));
        $this->assertEquals(
            array("count" => 2, "score" => array("10", "20")),
            array("count" => $target->getExecuteCount(), "score" => array($updated_brands_users_relation1->score, $updated_brands_users_relation2->score)));
    }

    public function testExecuteProcess04_whenNinetyNineInputAndOutput() {
        $target = new SetBrandFansScore();

        $brands_users_relations = array();
        $expected_scores = array();
        for ($i = 0 ; $i < 99 ; $i ++) {
            list($brand, $user, $brand_users_relation) = $this->newBrandToBrandUsersRelation();
            $this->executeQuery("UPDATE brands_users_relations SET login_count = 1 WHERE id = " . $brand_users_relation->id);
            $brands_users_relations[] = $brand_users_relation->id;
            $expected_scores[] = "10";
        }

        $target->doProcess();

        $rs = $this->executeQuery("SELECT score FROM brands_users_relations ORDER BY id");
        $actual_scores = array();
        while ($row = $this->fetchResultSet($rs)) {
            $actual_scores[] = $row['score'];
        }

        $this->assertEquals(
            array("count" => 99, "score" => $expected_scores),
            array("count" => $target->getExecuteCount(), "score" => $actual_scores));
    }

    public function testExecuteProcess05_whenOneHundredInputAndOutput() {
        $target = new SetBrandFansScore();

        $brands_users_relations = array();
        $expected_scores = array();
        for ($i = 0 ; $i < 100 ; $i ++) {
            list($brand, $user, $brand_users_relation) = $this->newBrandToBrandUsersRelation();
            $this->executeQuery("UPDATE brands_users_relations SET login_count = 1 WHERE id = " . $brand_users_relation->id);
            $brands_users_relations[] = $brand_users_relation->id;
            $expected_scores[] = "10";
        }

        $target->doProcess();

        $rs = $this->executeQuery("SELECT score FROM brands_users_relations ORDER BY id");
        $actual_scores = array();
        while ($row = $this->fetchResultSet($rs)) {
            $actual_scores[] = $row['score'];
        }

        $this->assertEquals(
            array("count" => 100, "score" => $expected_scores),
            array("count" => $target->getExecuteCount(), "score" => $actual_scores));
    }

    public function testExecuteProcess06_whenOneHundredOneInputAndOutput() {
        $target = new SetBrandFansScore();

        $brands_users_relations = array();
        $expected_scores = array();
        for ($i = 0 ; $i < 101 ; $i ++) {
            list($brand, $user, $brand_users_relation) = $this->newBrandToBrandUsersRelation();
            $this->executeQuery("UPDATE brands_users_relations SET login_count = 1 WHERE id = " . $brand_users_relation->id);
            $brands_users_relations[] = $brand_users_relation->id;
            $expected_scores[] = "10";
        }

        $target->doProcess();

        $rs = $this->executeQuery("SELECT score FROM brands_users_relations ORDER BY id");
        $actual_scores = array();
        while ($row = $this->fetchResultSet($rs)) {
            $actual_scores[] = $row['score'];
        }

        $this->assertEquals(
            array("count" => 101, "score" => $expected_scores),
            array("count" => $target->getExecuteCount(), "score" => $actual_scores));
    }
}