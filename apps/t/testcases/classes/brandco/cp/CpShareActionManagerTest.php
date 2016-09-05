<?php
/**
 * Created by IntelliJ IDEA.
 * User: le_tung
 * Date: 15/07/22
 * Time: 10:59
 */
AAFW::import('jp.aainc.classes.brandco.cp.CpShareActionManager');
require_once("CpActionManagerTestBase.php");

class CpShareActionManagerTest extends CpActionManagerTestBase {

    public function testDeletePhysicalRelatedCpActionData() {

        $this->deletePhysicalRelatedCpActionData_createData(CpAction::TYPE_SHARE);

    }

    public function deletePhysicalRelatedCpActionData_testFunction($brand, $cp, $cp_action_group, $cp_action,$user, $cp_user) {

        $share_action = $this->entity("CpShareActions", array("cp_action_id" => $cp_action->id));
        $share_user_log = $this->entity("CpShareUserLogs", array("cp_user_id" => $cp_user->id, "cp_share_action_id" => $share_action->id));

        $this->assertEquals($this->countEntities("CpShareUserLogs", array("id" => $share_user_log->id)), 1);

        $manager_class = new CpShareActionManager();
        $manager_class->deletePhysicalRelatedCpActionData($cp_action);

        $this->purge("CpShareActions", $share_action->id);
        $this->assertEquals($this->countEntities("CpShareUserLogs", array("id" => $share_user_log->id)), 0);
    }
}
