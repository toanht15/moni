<?php
/**
 * Created by IntelliJ IDEA.
 * User: le_tung
 * Date: 15/07/22
 * Time: 12:30
 */
AAFW::import('jp.aainc.classes.brandco.cp.CpAnnounceActionManager');
require_once("CpActionManagerTestBase.php");

class CpAnnounceActionManagerTest extends CpActionManagerTestBase {

    public function testDeletePhysicalRelatedCpActionData() {

        $this->deletePhysicalRelatedCpActionData_createData(CpAction::TYPE_ANNOUNCE);

    }

    public function deletePhysicalRelatedCpActionData_testFunction($brand, $cp, $cp_action_group, $cp_action,$user, $cp_user) {
        $manager_class = new CpAnnounceActionManager();
        $manager_class->deletePhysicalRelatedCpActionData($cp_action);
    }
}
