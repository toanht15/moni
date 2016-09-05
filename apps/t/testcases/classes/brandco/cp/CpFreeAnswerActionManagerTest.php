<?php
/**
 * Created by IntelliJ IDEA.
 * User: le_tung
 * Date: 15/07/22
 * Time: 10:59
 */
AAFW::import('jp.aainc.classes.brandco.cp.CpFreeAnswerActionManager');
require_once("CpActionManagerTestBase.php");

class CpFreeAnswerActionManagerTest extends CpActionManagerTestBase {

    public function testDeletePhysicalRelatedCpActionData() {

        $this->deletePhysicalRelatedCpActionData_createData(CpAction::TYPE_FREE_ANSWER);

    }

    public function deletePhysicalRelatedCpActionData_testFunction($brand, $cp, $cp_action_group, $cp_action,$user, $cp_user) {
        //create free answer record
        $answer = $this->entity("CpFreeAnswerActionAnswers", array("cp_user_id" => $cp_user->id, "cp_action_id" => $cp_action->id, "free_answer" => "TEST"));

        $this->assertEquals($this->countEntities("CpFreeAnswerActionAnswers", array("id" => $answer->id)), 1);

        $manager_class = new CpFreeAnswerActionManager();
        $manager_class->deletePhysicalRelatedCpActionData($cp_action);

        $this->assertEquals($this->countEntities("CpFreeAnswerActionAnswers", array("id" => $answer->id)), 0);
    }
}
