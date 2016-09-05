<?php

AAFW::import('jp.aainc.classes.batch.SyndotHealthChecker');


class SyndotHealthCheckerTest extends BaseTest{
    /** @var SyndotHealthChecker $target */
    private $target = null;

    public function setUp() {
        $this->target = new SyndotHealthChecker();
    }

    public function test_jsClickMenuのclassが含まれているときはtrue() {
        $this->assertTrue($this->target->isExistsAttributeSynMenuId("<a href=\"https://monipla.com/syndot-campaign/r/t_gunosy\" class=\"jsClickMenu\">"));
    }

    public function test_jsClickMenuのclassが含まれない時はfalse() {
        $this->assertFalse($this->target->isExistsAttributeSynMenuId("<a href=\"https://monipla.com/syndot-campaign/r/t_gunosy\">"));
    }
}