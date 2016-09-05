<?php
/**
 * Created by IntelliJ IDEA.
 * User: le_tung
 * Date: 15/07/22
 * Time: 10:59
 */
AAFW::import('jp.aainc.classes.brandco.cp.CpShippingAddressActionManager');
require_once("CpActionManagerTestBase.php");

class CpShippingAddressActionManagerTest extends CpActionManagerTestBase {

    public function testDeletePhysicalRelatedCpActionData() {

        $this->deletePhysicalRelatedCpActionData_createData(CpAction::TYPE_SHIPPING_ADDRESS);

    }

    public function deletePhysicalRelatedCpActionData_testFunction($brand, $cp, $cp_action_group, $cp_action,$user, $cp_user) {

        $shipping_action = $this->entity("CpShippingAddressActions", array("title" => "TEST", "cp_action_id" => $cp_action->id, "text" => "TEST"));
        $shipping_user = $this->entity("ShippingAddressUsers", array("cp_user_id" => $cp_user->id, "cp_shipping_address_action_id" => $shipping_action->id));

        $this->assertEquals($this->countEntities("ShippingAddressUsers", array("id" => $shipping_user->id)), 1);

        $manager_class = new CpShippingAddressActionManager();
        $manager_class->deletePhysicalRelatedCpActionData($cp_action);
        $this->purge("CpShippingAddressActions", $shipping_action->id);

        $this->assertEquals($this->countEntities("ShippingAddressUsers", array("id" => $shipping_user->id)), 0);
    }
}
