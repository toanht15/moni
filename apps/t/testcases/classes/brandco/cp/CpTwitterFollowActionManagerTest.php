<?php
/**
 * Created by IntelliJ IDEA.
 * User: le_tung
 * Date: 15/07/22
 * Time: 10:59
 */
AAFW::import('jp.aainc.classes.brandco.cp.CpTwitterFollowActionManager');
require_once("CpActionManagerTestBase.php");

class CpTwitterFollowActionManagerTest extends CpActionManagerTestBase {

    public function testDeletePhysicalRelatedCpActionData() {

        $this->deletePhysicalRelatedCpActionData_createData(CpAction::TYPE_TWITTER_FOLLOW);

    }

    public function deletePhysicalRelatedCpActionData_testFunction($brand, $cp, $cp_action_group, $cp_action,$user, $cp_user) {

        $tw_follow_action = $this->entity("CpTwitterFollowActions", array("cp_action_id" => $cp_action->id));
        $tw_follow_log = $this->entity("CpTwitterFollowLogs", array("cp_user_id" => $cp_user->id, "action_id" => $tw_follow_action->id));

        $this->assertEquals($this->countEntities("CpTwitterFollowLogs", array("id" => $tw_follow_log->id)), 1);

        $manager_class = new CpTwitterFollowActionManager();
        $manager_class->deletePhysicalRelatedCpActionData($cp_action);

        $this->purge("CpTwitterFollowActions", $tw_follow_action->id);
        $this->assertEquals($this->countEntities("CpTwitterFollowLogs", array("id" => $tw_follow_log->id)), 0);
    }
}
