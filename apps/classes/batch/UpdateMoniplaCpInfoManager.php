<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.services.monipla.UpdateMoniplaCpInfo');
AAFW::import('jp.aainc.classes.services.ApplicationService');

class UpdateMoniplaCpInfoManager {
    const DEFAULT_CHUNK_SIZE = 1000;

    /** @var CpMessageDeliveryService */
    private $cp_message_delivery_service;
    /** @var UpdateMoniplaCpInfo */
    private $update_monipla_cp_info;
    /** @var CpFlowService */
    private $cp_flow_service;

    /**
     * UpdateMoniplaCpInfoManager constructor.
     */
    public function __construct() {
        $service_factory = new aafwServiceFactory();
        $this->cp_message_delivery_service = $service_factory->create('CpMessageDeliveryService');
        $this->update_monipla_cp_info = $service_factory->create('UpdateMoniplaCpInfo');
        $this->cp_flow_service = $service_factory->create('CpFlowService');
    }

    public function doProcess() {

        $reservation = $this->cp_message_delivery_service->getDeliveredCpMsgDeliveryReservationByMoniplaUpdateStatus();

        if( !$reservation ) {
            return;
        }

        try {
            $cp_action = $this->cp_flow_service->getCpActionById($reservation->cp_action_id);
            $cp = $this->cp_flow_service->getCpByCpAction($cp_action);

            if( $this->validate($cp, $cp_action) ) {
                $this->sendAnnounceStatusToMedia($reservation, $cp, $cp_action);
                $reservation->monipla_update_status = CpMessageDeliveryReservation::MONIPLA_STATUS_UPDATED;
                $this->cp_message_delivery_service->updateCpMessageDeliveryReservation($reservation);
            } else {
                $reservation->monipla_update_status = CpMessageDeliveryReservation::MONIPLA_STATUS_SKIP;
                $this->cp_message_delivery_service->updateCpMessageDeliveryReservation($reservation);
            }

        } catch (Exception $e) {
            $reservation->monipla_update_status = CpMessageDeliveryReservation::MONIPLA_STATUS_UPDATE_FAILED;
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('UpdateMoniplaCpInfoManager@doProcess Error');
            $logger->error($e);
        }

        $this->cp_message_delivery_service->updateCpMessageDeliveryReservation($reservation);
    }

    /**
     * @param Cp $cp
     * @param CpAction $cp_action
     * @return bool
     */
    public function validate($cp, $cp_action) {
        if( $cp->isDemo() || $cp->isNonIncentiveCp() || ($cp->type != Cp::TYPE_CAMPAIGN) ) {
            return false;
        }

        if( $cp_action->type != CpAction::TYPE_ANNOUNCE ) {
            return false;
        }

        /** @var $brand Brand */
        $brand = $cp->getBrand();
        if( $brand->test_page == Brand::BRAND_TEST_PAGE ) {
            return false;
        }

        $cp_action_groups = $cp_action->getCpActionGroups();
        $cur_cp_action_group = $cp_action_groups->current();
        return $cur_cp_action_group->order_no != 1;
    }

    /**
     * @param $reservation CpMessageDeliveryReservation
     * @param $cp Cp
     * @param $cp_action CpAction
     * @throws Exception
     */
    public function sendAnnounceStatusToMedia($reservation, $cp, $cp_action) {
        $user_ids_array = array();
        $targets = $this->cp_message_delivery_service->getTargetsByReservationId($reservation->id);
        foreach ($targets as $target) {
            $user_ids_array[] = $target->user_id;
        }

        $chunked_user_ids = array_chunk($user_ids_array, self::DEFAULT_CHUNK_SIZE);
        $brand = $cp->getBrand();
        foreach ($chunked_user_ids as $user_ids) {
            $this->update_monipla_cp_info->sendCpUserAnnounceStatus(
                $user_ids,
                $reservation->cp_action_id,
                $brand->app_id,
                $cp_action->type
            );
        }
    }

}