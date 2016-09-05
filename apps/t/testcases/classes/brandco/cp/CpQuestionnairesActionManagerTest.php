<?php
/**
 * Created by IntelliJ IDEA.
 * User: le_tung
 * Date: 15/07/22
 * Time: 10:59
 */
AAFW::import('jp.aainc.classes.brandco.cp.CpQuestionnaireActionManager');
require_once("CpActionManagerTestBase.php");

class CpQuestionnairesActionManagerTest extends CpActionManagerTestBase {

    public function testDeletePhysicalRelatedCpActionData() {

        $this->deletePhysicalRelatedCpActionData_createData(CpAction::TYPE_QUESTIONNAIRE);

    }

    public function deletePhysicalRelatedCpActionData_testFunction($brand, $cp, $cp_action_group, $cp_action,$user, $cp_user) {
        $manager_class = new CpQuestionnaireActionManager();
        $manager_class->deletePhysicalRelatedCpActionData($cp_action);
    }
}
