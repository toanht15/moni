<?php

class CpActionsTest extends BaseTest {

    public function testLoadActionSpecificCatalogs01_loadAllVariants() {
        CpActions::loadCatalogs(array(
            0 => CpAction::TYPE_SHIPPING_ADDRESS,
            1 => CpAction::TYPE_MESSAGE,
            2 => CpAction::TYPE_QUESTIONNAIRE,
            3 => CpAction::TYPE_SHARE
        ));
        $this->assertTrue(true);
    }

    public function testLoadActionSpecificCatalogs02_loadEntry() {
        CpActions::loadCatalogs(array(0 => CpAction::TYPE_ENTRY));
        $this->assertTrue(true);
    }
}