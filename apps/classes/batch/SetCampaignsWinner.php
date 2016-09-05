<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class SetCampaignsWinner extends BrandcoBatchBase {

    const STAMP_PRIZE_ONE_REQUIRED = 20;
    const STAMP_PRIZE_TWO_REQUIRED = 10;

    const STAMP_RALLY_CP_TARGET_COUNT = 25; //スタンプラリーキャンペーンの当選者数
    const CP_TARGET_COUNT = 1; //普通なキャンペーンの当選者数

    const CP_ACTION_ID_STAMP_PRIZE_ONE = 1; //20個以上スタンプラリーのcp_action_id
    const CP_ACTION_ID_STAMP_PRIZE_TWO = 2; //10個以上スタンプラリーのcp_action_id

    private $db;
    private $cp_flow_service;

    public function __construct($argv) {
        parent::__construct($argv);
        $this->db = aafwDataBuilder::newBuilder();
        $this->cp_flow_service = $this->service_factory->create("CpFlowService");
    }

    public function executeProcess() {
        if (!$this->argv || count($this->argv) !== 3) {
            echo "brand_idやstart_date,end_date(m/d)を入力してください！\n";
            return;
        }

        $brand_id = $this->argv['brand_id'];
        $start_date = $this->convertDate($this->argv['start_date']);
        if (!$start_date) {
            echo "start_date invalid! (m/d)フォーマットで再入力してください！\n";
            return;
        }

        $end_date = $this->convertDate($this->argv['end_date']);
        if (!$end_date) {
            echo "end_date invalid! (m/d)フォーマットで再入力してください！\n";
            return;
        }

        //スタンプラリーキャンペーンの当選者をセットする
        $stamp_rally_targets = $this->setTargetForStampRallyCampaigns($brand_id, $start_date, $end_date);

        //普通なキャンペーンを取得する
        $campaigns = $this->cp_flow_service->getCpsByBrandIdAndPeriod($brand_id, $start_date, $end_date);

        //普通なキャンペーンの当選者をセットする
        foreach ($campaigns as $cp) {
            $last_action = $this->cp_flow_service->getLastActionOfFirstGroupByCpId($cp->id);
            $cp_target_user_id = $this->getTargetForNormalCpByRandom($last_action->id, $stamp_rally_targets, self::CP_TARGET_COUNT);

            if(!$cp_target_user_id){
                $this->logger->error("Set campaign winner failed! cp_id= ".$cp->id);
                continue;
            }

            $action_announce_id = $this->cp_flow_service->getCpActionIdsByCpIdAndType($cp->id, CpAction::TYPE_ANNOUNCE)[0];
            $this->setCampaignTarget($action_announce_id, $cp_target_user_id);
        }
    }

    /**
     * @param $input_date
     * @return bool|null|string
     */
    private function convertDate($input_date) {
        $time_stamp = strtotime($input_date);

        if (!$time_stamp) {
            return null;
        }
        $output_date = date("Y-m-d", $time_stamp);

        return $output_date;
    }

    /**
     * @param $brand_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    private function setTargetForStampRallyCampaigns($brand_id, $start_date, $end_date) {
        /** @var StaticHtmlStampRallyService $static_html_stamp_rally_service */
        $static_html_stamp_rally_service = $this->service_factory->create("StaticHtmlStampRallyService");
        $stamp_rally_cp_ids = $static_html_stamp_rally_service->getStampRallyCampaignsByBrandId($brand_id, $start_date, $end_date);

        //スタンプラリー20個以上参加したユーザーを取得する
        $prize_one_users = $this->getCpJoinedUserIdsByCpIdsAndJoinedCount($stamp_rally_cp_ids, self::STAMP_PRIZE_ONE_REQUIRED);

        //スタンプラリー10個以上参加したユーザーを取得する
        $prize_two_users = $this->getCpJoinedUserIdsByCpIdsAndJoinedCount($stamp_rally_cp_ids, self::STAMP_PRIZE_TWO_REQUIRED);

        $prize_one_targets = $this->getTargetUsersForStampRallyCpByRandom($prize_one_users, self::STAMP_RALLY_CP_TARGET_COUNT);

        foreach ($prize_one_targets as $target) {
            $this->setCampaignTarget(self::CP_ACTION_ID_STAMP_PRIZE_ONE, $target);
        }

        $prepare_prize_two_users = array_diff($prize_two_users, $prize_one_targets);
        $prize_two_targets = $this->getTargetUsersForStampRallyCpByRandom($prepare_prize_two_users, self::STAMP_RALLY_CP_TARGET_COUNT);

        foreach ($prize_two_targets as $target) {
            $this->setCampaignTarget(self::CP_ACTION_ID_STAMP_PRIZE_TWO, $target);
        }

        return array_merge($prize_one_targets, $prize_two_targets);
    }

    /**
     * @param $cp_ids
     * @param $joined_count
     * @return null
     */
    private function getCpJoinedUserIdsByCpIdsAndJoinedCount($cp_ids, $joined_count) {
        if (count($cp_ids) == 0) {
            return null;
        }
        //キャンペーンの最後cp_action_idを取得する
        $last_action_ids = array();

        foreach ($cp_ids as $cp_id) {
            $last_action = $this->cp_flow_service->getLastActionOfFirstGroupByCpId($cp_id);
            if ($last_action) {
                $last_action_ids[] = $last_action->id;
            }
        }

        if (count($last_action_ids) == 0) {
            return null;
        }

        //キャンペーンに参加したユーザーや参加数を取得する
        $condition = array(
            "cp_action_ids" => $last_action_ids,
            "status" => CpUserActionStatus::JOIN,
            "cp_joined_count" => $joined_count
        );

        $users =  $this->db->getCpsJoinedUsersAndCountByLastCpActionIds($condition);
        $user_ids = array();

        foreach ($users as $user){
            $user_ids[] = $user['user_id'];
        }

        return $user_ids;
    }

    /**
     * ランダムでスタンプラリーキャンペーンの当選者を取得する
     * @param $user_ids
     * @param $target_count
     * @return array
     */
    private function getTargetUsersForStampRallyCpByRandom($user_ids, $target_count) {
        $target_users = array();

        $random_ids = array_rand($user_ids, $target_count);

        if(!is_array($random_ids) && !Util::isNullOrEmpty($random_ids)){
            $target_users[] = $user_ids[$random_ids];

            return $target_users;
        }

        foreach ($random_ids as $random_id) {
            $target_users[] = $user_ids[$random_id];
        }

        return $target_users;
    }

    /**
     * キャンペーンの当選者をセットする
     * @param $cp_action_id
     * @param $user_id
     */
    private function setCampaignTarget($cp_action_id, $user_id) {
        /** @var CpMessageDeliveryService $cp_message_delivery_service */
        $cp_message_delivery_service = $this->service_factory->create("CpMessageDeliveryService");

        $reservation = $cp_message_delivery_service->getCurrentCpMessageDeliveryReservationByCpActionId($cp_action_id);

        if(!$reservation){
            $type = CpMessageDeliveryReservation::TYPE_IDS;
            $delivery_type = CpMessageDeliveryReservation::DELIVERY_TYPE_RESERVATION;
            $reservation = $cp_message_delivery_service->createCpMessageDeliveryReservation($cp_action_id, $type, null, $delivery_type);
        }

        if($cp_message_delivery_service->getCpMessageDeliveryTargetsByReservationIdAndUserIds($reservation->id, $user_id)){
            return;
        }

        $cp_message_delivery_service->createCpMessageDeliveryTarget($reservation->id, $cp_action_id, $user_id);
    }

    /**
     * ランダムで普通なキャンペーンの当選者を取得する
     * @param $cp_action_id
     * @param $user_ids
     * @param $limit
     * @return mixed
     */
    private function getTargetForNormalCpByRandom($cp_action_id, $user_ids, $limit) {
        $condition = array(
            "cp_action_id" => $cp_action_id,
            "status" => CpUserActionStatus::JOIN,
            "user_ids" => count($user_ids) != 0 ? $user_ids : null,
            "limit" => $limit
        );

        $result = $this->db->getCpJoinedUsersByLastActionId($condition);

        return $result[0]['user_id'];
    }
}