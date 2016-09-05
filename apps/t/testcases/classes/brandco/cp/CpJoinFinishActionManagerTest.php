<?php
/**
 * Created by IntelliJ IDEA.
 * User: le_tung
 * Date: 15/07/22
 * Time: 10:59
 */
AAFW::import('jp.aainc.classes.brandco.cp.CpJoinFinishActionManager');
require_once("CpActionManagerTestBase.php");

class CpJoinFinishActionManagerTest extends CpActionManagerTestBase {

    public function testDeletePhysicalRelatedCpActionData() {

        $this->deletePhysicalRelatedCpActionData_createData(CpAction::TYPE_JOIN_FINISH);

    }

    public function deletePhysicalRelatedCpActionData_testFunction($brand, $cp, $cp_action_group, $cp_action,$user, $cp_user) {
        $manager_class = new CpEntryActionManager();
        $manager_class->deletePhysicalRelatedCpActionData($cp_action);
    }
}
