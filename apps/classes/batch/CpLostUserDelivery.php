<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

require_once dirname(__FILE__) . '/../../config/define.php';

class CpLostUserDelivery {

    private $logger;
    private $hipchat_logger;
    private $service_factory;
    private $db;
    private $total_delivery_targets;

    public function __construct() {
        $this->service_factory = new aafwServiceFactory();
        $this->db = aafwDataBuilder::newBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function doProcess() {
        //対象のcp_actionを取得する
        $recent_delivered_cp_actions = $this->getRecentDeliveredCpActionTargets();
        $send_mail_targets = array();

        //全ての落選者メールの送る対象、
        $this->total_delivery_targets = array();

        foreach ($recent_delivered_cp_actions as $cp_action_id => $cp_id) {
            //cp_action_idで当選者を取得する
            $winners = $this->getWinnersByCpActionId($cp_action_id);

            //$cp_idの落選者通知メールの送る対象を取得する
            $delivery_user_targets = $this->getCpLostDeliveryTargets($cp_id, $winners);

            //全ての落選者メールの送る対象に追加する
            $this->total_delivery_targets = array_merge($this->total_delivery_targets, $delivery_user_targets);

            if (count($delivery_user_targets) == 0) {
                continue;
            }

            $send_mail_targets[] = array(
                'cp_id'                => $cp_id,
                'cp_action_id'         => $cp_action_id,
                'delivery_users_info'  => $delivery_user_targets
            );
        }

        //落選者通知メールを送信する
        $this->sendNotificationMails($send_mail_targets);
    }

    /**
     * 送信済メッセージの中から、落選通知を行っていない、かつ送信されてから1日以内のcp_action_idを取得します
     * @return array
     */
    private function getRecentDeliveredCpActionTargets() {
        $reservation_delivered_begin_date = date('Y-m-d', strtotime('-1 day'));

        $params = array(
            'exclude_delivery_type' => CpMessageDeliveryReservation::DELIVERY_TYPE_NONE,
            'updated_at_begin'      => $reservation_delivered_begin_date,
            'status'                => CpMessageDeliveryReservation::STATUS_DELIVERED
        );

        $order = array(
            'name' => 'delivered_at',
            'direction' => 'asc'
        );

        // 送信済、落選通知を行っていない、送信されてから1日以内、という条件でcp_action_idを抽出
        $delivered_cp_action_ids = $this->db->getRecentDeliveredCpMessageDeliveryReservation($params, $order);

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->service_factory->create('CpFlowService');

        $cp_action_targets = array();

        //当選通知では無いcp_actionを除外
        foreach ($delivered_cp_action_ids as $delivered_cp_action_id) {
            $cp_action_id = $delivered_cp_action_id['cp_action_id'];

            //すでに取得したcp_action_idをチェックする
            if (array_key_exists($cp_action_id, $cp_action_targets)) {
                continue;
            }

            //落選者通知メールを送る対象かどうかチェックする
            if (!$this->isTargetCpAction($cp_action_id)) {
                continue;
            }

            $cp_action = $cp_flow_service->getCpActionById($cp_action_id);
            $cp = $cp_flow_service->getCpByCpAction($cp_action);

            $cp_action_targets[$cp_action_id] = $cp->id;
        }

        return $cp_action_targets;
    }

    /**
     * $cp_action_idで当選者を取得する
     * @param $cp_action_id
     * @return array
     */
    private function getWinnersByCpActionId ($cp_action_id){
        /** @var CpMessageDeliveryService $cp_message_delivery_service */
        $cp_message_delivery_service = $this->service_factory->create('CpMessageDeliveryService');

        //当選通知メールを送信した対象を取得する
        $delivered_targets = $cp_message_delivery_service->getDeliveredTargetsByCpActionId($cp_action_id);
        $winners = array();

        foreach ($delivered_targets as $target){
            $winners[] = $target->user_id;
        }

        return $winners;
    }

    /**
     * キャンペーンの落選者通知対象を取得する
     * @param $cp_id
     * @param $winners
     * @return array
     */
    private function getCpLostDeliveryTargets($cp_id, $winners) {

        $params = array(
            'cp_id'                          => $cp_id,
            'EXCLUDE_CP_LOST_DELIVERY_USERS' => "__ON__",   //すでに落選者通知メールをもらったユーザーを除く
            'GET_NEW_CREATED_USER'           => "__ON__"    // 過去に登録したユーザーに送らないため、キャンペーンの開始したあと、新登録ユーザーのみ取得する
        );

        //キャンペーンの当選者を除く
        if (count($winners) > 0) {
            $params['EXCLUDE_CP_WINNER'] = "__ON__";
            $params['cp_winners'] = $winners;
        }

        //キャンペーンの落選者を取得する
        $cp_lost_users = $this->db->getCpLostUsersByCpId($params);

        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->service_factory->create('CpUserService');
        $cp_lost_delivery_targets = array();

        //落選者通知メールの対象ではないuser_idを除外する
        foreach ($cp_lost_users as $cp_lost_user) {

            //他のキャンペーンの落選者通知メールの送る対象
            if(in_array($cp_lost_user['user_id'], $this->total_delivery_targets)){
                continue;
            }

            //キャンペーンに参加完了したかどうかチェックする (エントリーモジュールが完了したユーザー)
            $is_join_finish = $cp_user_service->isJoinedCp($cp_id, $cp_lost_user['user_id']);

            if (!$is_join_finish) {
                continue;
            }

            $user_info = $this->getUserInfoByUserId($cp_lost_user['user_id']);

            //メールアドレスが無いユーザーはメール送信対象ではない
            if(!$user_info['mail_address']) {
                continue;
            }

            $cp_lost_delivery_targets[] = $user_info;
        }

        return $cp_lost_delivery_targets;
    }

    /**
     * モニプラコアーからユーザー情報を取得する
     * @param $user_id
     * @return null
     */
    private function getUserInfoByUserId ($user_id) {
        /** @var UserService $user_service */
        $user_service = $this->service_factory->create('UserService');
        /** @var BrandcoAuthService $brandco_auth_service */
        $brandco_auth_service = $this->service_factory->create('BrandcoAuthService');

        $user = $user_service->getUserByBrandcoUserId($user_id);

        if(!$user) return null;

        $monipla_user_info = $brandco_auth_service->getUserInfoByQuery($user->monipla_user_id);

        $user_info = array();
        $user_info['id'] = $user_id;
        $user_info['name'] = $monipla_user_info->name;
        $user_info['mail_address'] = $monipla_user_info->mailAddress;

        return $user_info;
    }

    /**
     * 落選者へのメールを送る対象かどうかチェックする
     * @param $cp_action_id
     * @return bool
     */
    private function isTargetCpAction($cp_action_id) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->service_factory->create('CpFlowService');
        $cp_action_group = $cp_flow_service->getCpActionGroupByAction($cp_action_id);

        //cp_action_idの所属するグル-プ内で当選通知モジュールがあるかどうかチェックする
        $cp_announce_action = $cp_flow_service->getCpActionByCpActionGroupIdAndType($cp_action_group->id, CpAction::TYPE_ANNOUNCE);

        if(!$cp_announce_action) {
            return false;
        }

        //落選者へのメールを送る対象キャンペーンかどうかチェックする
        $cp_action = $cp_flow_service->getCpActionById($cp_action_id);
        $cp = $cp_flow_service->getCpByCpAction($cp_action);

        //発送をもって発表キャンペーンの場合はfalseを戻す
        if ($cp->shipping_method == Cp::SHIPPING_METHOD_PRESENT) {
            return false;
        }

        //スピードくじキャンペーンの場合はfalseを戻す
        if ($cp_flow_service->getCpActionIdsByCpIdAndType($cp->id, CpAction::TYPE_INSTANT_WIN)) {
            return false;
        }

        $announce_action_ids = $cp_flow_service->getCpActionIdsByCpIdAndType($cp->id, CpAction::TYPE_ANNOUNCE);

        //先着当選キャンペーンの場合はfalseを戻す
        foreach ($announce_action_ids as $announce_action_id) {
            $cp_action_group = $cp_flow_service->getCpActionGroupByAction($announce_action_id);

            if ($cp_action_group->order_no == 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * 落選者対象にメールを送信する
     * @param $send_mail_targets
     */
    private function sendNotificationMails($send_mail_targets) {
        /** @var UserMailService $user_mail_service */
        $user_mail_service = $this->service_factory->create('UserMailService');

        /** @var CpLostNotificationService $cp_lost_notification_service */
        $cp_lost_notification_service = $this->service_factory->create('CpLostNotificationService');

        /** @var CpLostNotificationUserService $cp_lost_notification_user_service */
        $cp_lost_notification_user_service = $this->service_factory->create('CpLostNotificationUserService');

        //メール送信失敗対象のcp_action_id
        $delivery_failed_targets = array();

        foreach ($send_mail_targets as $send_mail_target) {
            try {
                foreach ($send_mail_target['delivery_users_info'] as $user_info) {
                    $is_send_mail_success = $user_mail_service->sendCpLostNotificationMail($user_info, $send_mail_target['cp_id']);

                    //送信失敗
                    if (!$is_send_mail_success) {
                        $delivery_failed_targets[] = $send_mail_target['cp_action_id'];
                        $this->logger->error('CpLostUserDelivery#sendNotificationMails() send mail failed! user_id=' . $user_info['id'] . ' , cp_action_id =' . $send_mail_target['cp_action_id'] . ' - $cp_id=' . $send_mail_target['cp_id']);
                        continue;
                    }

                    $cp_lost_notification = $cp_lost_notification_service->updateCpLostNotification(array(
                        'cp_action_id' => $send_mail_target['cp_action_id'],
                        'notified' => CpLostNotification::NOTIFIED_SUCCESS
                    ));

                    $cp_lost_notification_user_service->createCpLostNotificationUser(array(
                        'cp_lost_notification_id' => $cp_lost_notification->id,
                        'user_id' => $user_info['id']
                    ));
                }

            } catch (aafwException $e) {
                $cp_lost_notification_service->updateCpLostNotification(array(
                    'cp_action_id' => $send_mail_target['cp_action_id'],
                    'notified' => CpLostNotification::NOTIFIED_FAILED
                ));

                $delivery_failed_targets[] = $send_mail_target['cp_action_id'];
                $this->logger->error('CpLostUserDelivery#sendNotificationMails() send mail failed! cp_action_id =' . $send_mail_target['cp_action_id'] . ' - $cp_id=' . $send_mail_target['cp_id']);
                $this->logger->error($e);
            }
        }

        //メール送信失敗対象がある場合は、hipchatでエラーメッセージを送る
        if(count($delivery_failed_targets) > 0){
            $this->hipchat_logger->error('ERROR CpLostUserDelivery#sendNotificationMails() Send Mail Failed! cp_action_id = '.implode(",", array_unique($delivery_failed_targets)));
        }
    }
}