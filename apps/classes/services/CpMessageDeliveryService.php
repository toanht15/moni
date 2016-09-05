<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpMessageDeliveryReservationTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpMessageDeliveryTargetTrait');

class CpMessageDeliveryService extends aafwServiceBase {

    use CpMessageDeliveryReservationTrait;
    use CpMessageDeliveryTargetTrait;

    protected $cache_manager;

    /*** @var BrandsUsersRelationService relation_service */
    private $relation_service;
    private $logger;

    const ADD_TARGET = '1';
    const DELETE_TARGET = '2';
    const RANDOM_TARGET = '3';
    const FIX_TARGET = '4';
    const CANCEL_FIX_TARGET = '5';

    public function __construct() {

        $this->reservations = $this->getModel("CpMessageDeliveryReservations");
        $this->targets = $this->getModel("CpMessageDeliveryTargets");
        $this->relation_service = (new aafwServiceFactory ())->create('BrandsUsersRelationService');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function getCacheManager() {
        if (!$this->cache_manager) {
            $this->cache_manager = new CacheManager();
        }
        return $this->cache_manager;
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCurrentReservation($cp_action_id) {
        return $this->getCurrentCpMessageDeliveryReservationByCpActionId($cp_action_id);
    }


    /**
     * @param $brand_id
     * @param $cp_action_id
     * @return mixed
     */
    public function getOrCreateCurrentReservation($brand_id, $cp_action_id) {
        $reservation = $this->getCurrentReservation($cp_action_id);
        if ($reservation == null) {
            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $this->getService('CpFlowService');
            $cp_action = $cp_flow_service->getCpActionById($cp_action_id);

            if ($cp_action->type == CpAction::TYPE_ANNOUNCE_DELIVERY) {
                $reservation = $this->createReservation(
                    $brand_id,
                    $cp_action_id,
                    CpMessageDeliveryReservation::TYPE_IDS,
                    CpMessageDeliveryReservation::DELIVERY_DATE_NOT_SEND,
                    null,
                    null,
                    CpMessageDeliveryReservation::DELIVERY_TYPE_NONE
                );
            } else {
                $reservation = $this->createReservation(
                    $brand_id,
                    $cp_action_id,
                    CpMessageDeliveryReservation::TYPE_IDS,
                    '0000-00-00 00:00:00',
                    null,
                    null
                );
            }
        }
        return $reservation;
    }

    /**
     * @param $brand_id
     * @param $cp_action_id
     * @param $type
     * @param $delivery_date
     * @param null $conditions
     * @param null $user_ids
     * @param $delivery_type
     * @return mixed
     * TODO エラーを投げる
     */
    public function createReservation($brand_id, $cp_action_id, $type, $delivery_date, $conditions = null, $user_ids = null, $delivery_type = CpMessageDeliveryReservation::DELIVERY_TYPE_IMMEDIATELY) {

        $reservation = $this->createCpMessageDeliveryReservation($cp_action_id, $type, $delivery_date, $delivery_type);

        $targets = [];

        if ($type == CpMessageDeliveryReservation::TYPE_ALL) {
            $targets = $this->relation_service->getBrandsUsersRelationsByBrandId($brand_id);
        }

        if ($type == CpMessageDeliveryReservation::TYPE_IDS) {
            $targets = $this->relation_service->getBrandsUsersRelationsByBrandIdAndUserIds($brand_id, $user_ids);
        }

        if ($type == CpMessageDeliveryReservation::TYPE_SEARCH) {
            $targets = $this->relation_service->getBrandsUsersRelationsByConditions($conditions);
        }

        foreach ($targets as $target) {
            $this->createCpMessageDeliveryTarget($reservation->id, $reservation->cp_action_id, $target->user_id);
        }

        return $reservation;
    }

    /**
     * @param $brand_id
     * @param $reservation_id
     * @param bool $fix_target_flg
     * @return mixed
     * @throws Exception
     */
    public function getTargetsCount($brand_id, $reservation_id, $fix_target_flg = false) {
        AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
        $db = new aafwDataBuilder;

        $filter = array(
            'brand_id' => $brand_id,
            'reservation_id' => $reservation_id,
        );

        $filter['FIX_TARGET'] = "__ON__";
        if($fix_target_flg) {
            $filter['fix_target_flg'] = CpMessageDeliveryTarget::FIX_TARGET_ON;
        } else {
            $filter['fix_target_flg'] = CpMessageDeliveryTarget::FIX_TARGET_OFF;
        }

        $targets_count = $db->getReservationTargets($filter)[0]['cnt'];

        return $targets_count;
    }

    /**
     * @param $reservation_id
     * @return bool
     */
    public function checkFixedTargetByReservationId($reservation_id){
        $targets = $this->getTargetsByReservationId($reservation_id);

        if(count($targets) == 0){
            return false;
        }

        foreach ($targets as $target){
            if(!$target->fix_target_flg){
                return false;
            }
        }

        return true;
    }

    /**
     * @param $cp_action_id
     * @return bool
     */
    public function checkExistFixedTargetByCpActionId($cp_action_id) {
        $reservations = $this->getCpMessageDeliveryReservationsByCpActionId($cp_action_id);

        foreach ($reservations as $reservation) {
            $target = $this->getFixedTargetByReservationId($reservation->id);
            if ($target) return true;
        }

        return false;
    }

    /**
     * @param $cp_action_id
     * @return array
     */
    public function getFixedTargetUserIdByCpActionId($cp_action_id) {
        $user_ids = array();

        $reservations = $this->getCpMessageDeliveryReservationsByCpActionId($cp_action_id);

        foreach ($reservations as $reservation){
            $targets = $this->getTargetsByReservationId($reservation->id);

            foreach ($targets as $target) {
                if ($target->fix_target_flg == CpMessageDeliveryTarget::FIX_TARGET_ON) {
                    $user_ids[] = intval($target->user_id);
                }
            }
        }

        return $user_ids;
    }

    /**
     * @param $reservation_id
     * @return mixed
     */
    public function getTargetsCountByReservationId($reservation_id) {
        return $this->getCpMessageDeliveryTargetsCountByReservationId($reservation_id);
    }

    public function getTargetsCountByActionId($action_id) {
        $reservations = $this->getCpMessageDeliveryReservationsByCpActionId($action_id);
        $count = 0;
        foreach ($reservations as $reservation) {
            $count += $this->getTargetsCountByReservationId($reservation->id);
        }
        return $count;
    }

    /**
     * @param $cp_action_id
     */
    public function deletePhysicalDeliveryTargetsByCpActionId ($cp_action_id) {
        $targets = $this->targets->find(array("cp_action_id" => $cp_action_id));
        if (!$targets) {
            return;
        }
        foreach ($targets as $target) {
            $this->targets->deletePhysical($target);
        }
    }

    public function deletePhysicalDeliveryTargetsByCpActionIdAndUserId ($cp_action_id, $user_id) {
        if (!$cp_action_id || !$user_id) {
            return;
        }
        $targets = $this->targets->find(array("cp_action_id" => $cp_action_id, "user_id" => $user_id));
        if (!$targets) {
            return;
        }
        foreach ($targets as $target) {
            $this->targets->deletePhysical($target);
        }
        $this->getCacheManager()->deleteMessageHistoryCache($cp_action_id);
    }

    /**
     * @param $cp_action_id
     */
    public function deletePhysicalDeliveryReservationByCpActionId ($cp_action_id) {
        $reservations = $this->reservations->find(array("cp_action_id" => $cp_action_id));
        if (!$reservations) {
            return;
        }
        foreach ($reservations as $reservation) {
            $this->reservations->deletePhysical($reservation);
        }
        $this->getCacheManager()->deleteMessageHistoryCache($cp_action_id);
    }

    /**
     * @param $cp_action_id
     */
    public function deletePhysicalDeliveryReservationAndTargetsByCpActionId ($cp_action_id) {

        if (!$cp_action_id) {
            return;
        }

        $this->deletePhysicalDeliveryTargetsByCpActionId($cp_action_id);
        $this->deletePhysicalDeliveryReservationByCpActionId($cp_action_id);
    }
}
