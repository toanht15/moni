<?php
AAFW::import('jp.aainc.classes.entities.cp');

class CampaignHelper extends aafwObject {

    private $common_filter = array();
    const BRAND_ID = 1;

    public function __construct() {
        $this->common_filter = array('order' => array('name' => 'id', 'direction' => 'desc'));
    }

    /**
     * キャンペーンを作成する
     * @param $brand_id
     * @param array $cp_data
     * array $cp_data ex) array('cps_type' => 1, 'skeleton_type' => 4, 'announce_type' => 0, 'groupCount' => 2, 'group1' => 0,9, 'group2' => 3)
     * @param $cps_type
     * @param $join_limit_flg
     * @return mixed
     */
    public function createCampaign($brand_id, $cp_data = array(), $cps_type = cp::TYPE_CAMPAIGN, $join_limit_flg = cp::JOIN_LIMIT_OFF) {
        $skelton = new CpNewSkeletonCreator();
        return $skelton->create($brand_id, $cp_data, $cps_type, $join_limit_flg);
    }

    /**
     * キャンペーンに関連する全データを物理削除する
     */
    public function cleanupCampaigns() {
        /** @var Cps $cps_store */
        $cps_store = $this->getModel('Cps');

        try {
            $cps_store->begin();

            $cps_store = $this->getModel('Cps');
            $cps = $cps_store->find($this->common_filter);
            $this->deleteCps($cps);

            $cps_store->commit();
        } catch (Exception $e) {
            $cps_store->rollback();
            aafwLog4phpLogger::getDefaultLogger()->error('CampaignHelper#cleanupCampaigns error.' . $e);
        }
    }

    private function deleteCps($cps) {
        foreach ($cps as $cp) {
            $cp_action_groups = $cp->getCpActionGroups($this->common_filter);
            $this->deleteCpActionGroups($cp_action_groups);
            $cps = $this->getModel('Cps');

            /** @var CpUsers $cp_user */
            $cp_users = $this->getModel('CpUsers');
            if ($cp->isExistsCpUsers()) {
                foreach ($cp->getCpUsers() as $cp_user) {
                    $cp_users->deletePhysical($cp_user);
                }
            }
            $cps->deletePhysical($cp);
        }
    }

    private function deleteCpActionGroups($cp_action_groups) {
        foreach ($cp_action_groups as $cp_action_group) {
            $cp_actions = $cp_action_group->getCpActions($this->common_filter);
            $this->deleteCpActions($cp_actions);
            $cp_action_groups = $this->getModel('CpActionGroups');
            $cp_action_groups->deletePhysical($cp_action_group);
        }
    }

    /**
     * CpActionに関連するデータを削除する
     * @param $cp_actions
     */
    private function deleteCpActions($cp_actions) {
        //delete user action status and message
        /** @var CpUserActionStatusService $cp_user_action_status_service */
        $cp_user_action_status_service = $this->getService('CpUserActionStatusService');

        foreach ($cp_actions as $cp_action) {
            $action_manager = $cp_action->getActionManagerClass();
            // cp_action に関連するデータを物理削除する
            $action_manager->deletePhysicalRelatedCpActionData($cp_action, true);

            $cp_user_action_status_service->deleteCpUserActionMessagesByCpActionId($cp_action);
            $cp_user_action_status_service->deleteCpUserActionStatusByCpActionId($cp_action);

            /** @var CpNextActions $cp_next_actions */
            $cp_next_actions = $this->getModel('CpNextActions');
            $cp_next_action = $cp_next_actions->findOne(array('cp_action_id' => $cp_action->id));
            if ($cp_next_action->id) {
                $cp_next_actions->deletePhysical($cp_next_action);
            }

            if ($cp_action->isExistsCpMessageDeliveryTargets()) {
                foreach ($cp_action->getCpMessageDeliveryTargets() as $target) {
                    /** @var CpMessageDeliveryTargets $cp_message_delivery_targets */
                    $cp_message_delivery_targets = $this->getModel('CpMessageDeliveryTargets');
                    $cp_message_delivery_targets->deletePhysical($target);
                }
            }

            if ($cp_action->isExistsCpMessageDeliveryReservations()) {
                /** @var CpMessageDeliveryReservations $cp_message_delivery_reservations */
                $cp_message_delivery_reservations = $this->getModel('CpMessageDeliveryReservations');
                foreach ($cp_action->getCpMessageDeliveryReservations() as $cp_message_delivery_reservation) {
                    $cp_message_delivery_reservations->deletePhysical($cp_message_delivery_reservation);
                }
            }
        }
        $this->deleteAllCpActions($cp_actions);
    }

    /**
     * キャンペーンに関連する全ユーザデータを物理削除する
     */
    public function cleanupCampaignUsers() {
        /** @var Cps $cps_store */
        $cps_store = $this->getModel('Cps');

        try {
            $cps_store->begin();

            $cps_store = $this->getModel('Cps');
            $cps = $cps_store->find($this->common_filter);

            foreach ($cps as $cp) {
                $cp_action_groups = $cp->getCpActionGroups($this->common_filter);
                $this->callCpActionGroups($cp_action_groups);

                /** @var CpUsers $cp_user */
                $cp_users = $this->getModel('CpUsers');
                if ($cp->isExistsCpUsers()) {
                    foreach ($cp->getCpUsers() as $cp_user) {
                        $cp_users->deletePhysical($cp_user);
                    }
                }
            }

            $cps_store->commit();
        } catch (Exception $e) {
            $cps_store->rollback();
            aafwLog4phpLogger::getDefaultLogger()->error('CampaignHelper#cleanupCampaigns error.' . $e);
        }
    }

    private function callCpActionGroups($cp_action_groups) {
        foreach ($cp_action_groups as $cp_action_group) {
            $cp_actions = $cp_action_group->getCpActions($this->common_filter);
            $this->deleteCpActionUsers($cp_actions);
        }
    }

    /**
     * CpActionに関連するデータを削除する
     * @param $cp_actions
     */
    private function deleteCpActionUsers($cp_actions) {
        /** @var CpUserActionStatusService $cp_user_action_status_service */
        $cp_user_action_status_service = $this->getService('CpUserActionStatusService');

        foreach ($cp_actions as $cp_action) {
            $action_manager = $cp_action->getActionManagerClass();
            // cp_action に関連するデータを物理削除する
            $action_manager->deletePhysicalRelatedCpActionData($cp_action);

            //delete user action status and message
            $cp_user_action_status_service->deleteCpUserActionMessagesByCpActionId($cp_action);
            $cp_user_action_status_service->deleteCpUserActionStatusByCpActionId($cp_action);

            if ($cp_action->isExistsCpMessageDeliveryTargets()) {
                foreach ($cp_action->getCpMessageDeliveryTargets() as $target) {
                    /** @var CpMessageDeliveryTargets $cp_message_delivery_targets */
                    $cp_message_delivery_targets = $this->getModel('CpMessageDeliveryTargets');
                    $cp_message_delivery_targets->deletePhysical($target);
                }
            }

            if ($cp_action->isExistsCpMessageDeliveryReservations()) {
                /** @var CpMessageDeliveryReservations $cp_message_delivery_reservations */
                $cp_message_delivery_reservations = $this->getModel('CpMessageDeliveryReservations');
                foreach ($cp_action->getCpMessageDeliveryReservations() as $cp_message_delivery_reservation) {
                    $cp_message_delivery_reservations->deletePhysical($cp_message_delivery_reservation);
                }
            }
        }
    }

    /**
     * キャンペーンに関連する全ユーザデータを物理削除する
     */
    public function cleanupCampaignUsersByCpId($cp_id) {
        /** @var Cps $cps_store */
        $cps_store = $this->getModel('Cps');

        try {
            $cps_store->begin();

            $cps_store = $this->getModel('Cps');
            $filter = $this->common_filter + array('conditions' => array('id' => $cp_id));
            $cp = $cps_store->findOne($filter);

            $cp_action_groups = $cp->getCpActionGroups($this->common_filter);
            $this->callCpActionGroups($cp_action_groups);

            /** @var CpUsers $cp_user */
            $cp_users = $this->getModel('CpUsers');
            if ($cp->isExistsCpUsers()) {
                foreach ($cp->getCpUsers() as $cp_user) {
                    $cp_users->deletePhysical($cp_user);
                }
            }

            $cps_store->commit();
        } catch (Exception $e) {
            $cps_store->rollback();
            aafwLog4phpLogger::getDefaultLogger()->error('CampaignHelper#cleanupCampaigns error.' . $e);
        }
    }

    /**
     * キャンペーンに関連するデータを物理削除する
     * @param $brand_id
     */
    public function cleanupCampaignsByBrandId($brand_id) {
        /** @var Cps $cps_transaction */
        $cps_transaction = aafwEntityStoreFactory::create('Cps');

        try {
            $cps_transaction->begin();

            $filter = $this->common_filter + array('conditions' => array('brand_id' => $brand_id));
            $cps_store = $this->getModel('Cps');
            $cps = $cps_store->find($filter);
            $this->deleteCps($cps);

            $cps_transaction->commit();
        } catch (Exception $e) {
            $cps_transaction->rollback();
            aafwLog4phpLogger::getDefaultLogger()->error('CampaignHelper#cleanupCampaigns error.' . $e);
        }
    }

    /**
     * キャンペーンに関連するデータを物理削除する
     * @param $cp_id
     */
    public function cleanupCampaignByCpId($cp_id) {
        /** @var Cps $cps_transaction */
        $cps_transaction = aafwEntityStoreFactory::create('Cps');

        try {
            $cps_transaction->begin();

            $filter = $this->common_filter + array('conditions' => array('id' => $cp_id),);
            $cps_store = $this->getModel('Cps');
            $cp = $cps_store->findOne($filter);
            $this->deleteCp($cp);

            $cps_transaction->commit();
        } catch (Exception $e) {
            $cps_transaction->rollback();
            aafwLog4phpLogger::getDefaultLogger()->error('CampaignHelper#cleanupCampaigns error.' . $e);
        }
    }

    private function deleteCp($cp) {
        $cp_action_groups = $cp->getCpActionGroups($this->common_filter);
        $this->deleteCpActionGroups($cp_action_groups);
        $cps = $this->getModel('Cps');

        /** @var CpUsers $cp_user */
        $cp_users = $this->getModel('CpUsers');
        if ($cp->isExistsCpUsers()) {
            foreach ($cp->getCpUsers() as $cp_user) {
                $cp_users->deletePhysical($cp_user);
            }
        }
        $cps->deletePhysical($cp);
    }

    /**
     * cp_next_actionsがidを保持している可能性があるので別でループして削除する
     * @param $cp_actions
     */
    private function deleteAllCpActions($cp_actions) {
        foreach ($cp_actions as $cp_action) {
            /** @var CpActions $cp_actions */
            $cp_actions = $this->getModel('CpActions');
            $cp_actions->deletePhysical($cp_action);
        }
    }
}