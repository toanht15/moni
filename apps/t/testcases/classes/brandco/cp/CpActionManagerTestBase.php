<?php
/**
 * Created by IntelliJ IDEA.
 * User: le_tung
 * Date: 15/07/22
 * Time: 12:33
 */
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpCreator');
AAFW::import('jp.aainc.classes.services.CpUserActionStatusService');

abstract class CpActionManagerTestBase extends BaseTest {

    public function deletePhysicalRelatedCpActionData_createData($cp_action_type) {
        //設定
        $this->clearBrandAndRelatedEntities();
        list ($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $this->updateEntities("CpActions", array("id" => $cp_action->id), array("type" => $cp_action_type, "status" => CpAction::STATUS_FIX));
        $user = $this->newUser();
        $cp_user = $this->entity("CpUsers", array("cp_id" => $cp->id, "user_id" => $user->id));
        $cp_user_message = $this->entity("CpUserActionMessages", array("cp_user_id" => $cp_user->id, "cp_action_id" => $cp_action->id, "title" => "TEST"));
        $cp_user_status = $this->entity("CpUserActionStatuses", array("cp_user_id" => $cp_user->id, "cp_action_id" => $cp_action->id, "title" => "TEST"));

        //テスト実装
        $this->deletePhysicalRelatedCpActionData_testFunction($brand, $cp, $cp_action_group, $cp_action,$user , $cp_user);
        /** @var CpUserActionStatusService $cp_user_action_status_service */
        $cp_user_action_status_service = new CpUserActionStatusService();
        // remove action status and message + cache
        $cp_user_action_status_service->deleteCpUserActionMessagesByCpActionId($cp_action->id);
        $cp_user_action_status_service->deleteCpUserActionStatusByCpActionId($cp_action->id);

        $this->assertEquals($this->countEntities("CpUserActionMessages", array("id" => $cp_user_message->id)), 0);
        $this->assertEquals($this->countEntities("CpUserActionStatuses", array("id" => $cp_user_status->id)), 0);
    }
}
