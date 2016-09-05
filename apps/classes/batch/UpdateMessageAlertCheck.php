<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.services.MessageAlertCheckService');
AAFW::import('jp.aainc.classes.services.CpMessageDeliveryService');
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
AAFW::import('jp.aainc.classes.services.UserService');

class UpdateMessageAlertCheck extends BrandcoBatchBase {

    const MAX_SEND_COUNT = 5;

    const NO_CHECKED = 0;
    const CHECKED = 1;

    private $db;
    private $messageAlertCheckService;

    function __construct($argv = null) {
        parent::__construct($argv);
        $this->db = aafwDataBuilder::newBuilder();
        $this->messageAlertCheckService = new MessageAlertCheckService();
    }

    function executeProcess() {

        // 過去1日以内に送信されたメッセージ情報
        $listReservations = $this->getListReservations();
        if($listReservations) {
            foreach($listReservations as $key => $value) {
                $reservation_ids[$key] = $value['reservation_id'];
            }

            $listDeliveringReservations = $this->getListDeliveringReservation($reservation_ids);
            $listDeliveredReservations = $this->getListDeliveredReservation($reservation_ids);
            $listDeliveryFailReservations = $this->getListDeliveryFailReservation($reservation_ids);
        }

        $listSendMailFailCps = $this->getListSendMailFailCp();

        foreach($listDeliveringReservations as $reservation) {
            $list_key = array_search($reservation['reservation_id'], $reservation_ids);
            unset($listReservations[$list_key]);
            $deliveringAlertCheck = $this->messageAlertCheckService->getMessageAlertCheck($reservation['reservation_id']);
            if($deliveringAlertCheck) {
                if($deliveringAlertCheck->count >= self::MAX_SEND_COUNT) {
                    // 5回以上はアラートを出さない
                    continue;
                }
                $deliveringAlertCheck->count = $deliveringAlertCheck->count + 1;
                $this->messageAlertCheckService->updateMessageAlertCheck($deliveringAlertCheck);
            } else {
                $this->messageAlertCheckService->addMessageAlertCheck($reservation['reservation_id'], $reservation['cp_id'], self::NO_CHECKED);
            }
        }

        foreach($listDeliveredReservations as $reservation) {
            $list_key = array_search($reservation['reservation_id'], $reservation_ids);
            unset($listReservations[$list_key]);
            $deliveredAlertCheck = $this->messageAlertCheckService->getMessageAlertCheck($reservation['reservation_id']);
            if($deliveredAlertCheck) {
                if($deliveredAlertCheck->count >= self::MAX_SEND_COUNT) {
                    // 5回以上はアラートを出さない
                    continue;
                }
                $deliveredAlertCheck->count = $deliveredAlertCheck->count + 1;
                $this->messageAlertCheckService->updateMessageAlertCheck($deliveredAlertCheck);
            } else {
                $this->messageAlertCheckService->addMessageAlertCheck($reservation['reservation_id'], $reservation['cp_id'], self::NO_CHECKED);
            }
        }

        foreach($listDeliveryFailReservations    as $reservation) {
            $list_key = array_search($reservation['reservation_id'], $reservation_ids);
            unset($listReservations[$list_key]);
            $failAlertCheck = $this->messageAlertCheckService->getMessageAlertCheck($reservation['reservation_id']);
            if($failAlertCheck) {
                if($failAlertCheck->count >= self::MAX_SEND_COUNT) {
                    // 5回以上はアラートを出さない
                    continue;
                }
                $failAlertCheck->count = $failAlertCheck->count + 1;
                $this->messageAlertCheckService->updateMessageAlertCheck($failAlertCheck);
            } else {
                $this->messageAlertCheckService->addMessageAlertCheck($reservation['reservation_id'], $reservation['cp_id'], self::NO_CHECKED);
            }
        }

        foreach($listSendMailFailCps as $failCp) {
            $failCpAlertCheck = $this->messageAlertCheckService->getSendMailAlertCheck($failCp['cp_id']);
            if($failCpAlertCheck) {
                if($failCpAlertCheck->count >= self::MAX_SEND_COUNT) {
                    // 5回以上はアラートを出さない
                    continue;
                }
                $failCpAlertCheck->count = $failCpAlertCheck->count + 1;
                $this->messageAlertCheckService->updateMessageAlertCheck($failCpAlertCheck);
            } else {
                $this->messageAlertCheckService->addMessageAlertCheck(0, $failCp['cp_id'], self::NO_CHECKED);
            }
        }

        foreach($listReservations as $listReservation) {
            // 送信中で問題ないものは送信後のチェックもする必要があるのでここでは保存しない
            if($listReservation['status'] == CpMessageDeliveryReservation::STATUS_DELIVERING) {
                continue;
            }
            $failAlertCheck = $this->messageAlertCheckService->getMessageAlertCheck($listReservation['reservation_id']);
            if($failAlertCheck) {
                $failAlertCheck->checked = self::CHECKED;
                $this->messageAlertCheckService->updateMessageAlertCheck($failAlertCheck);
            } else {
                $this->messageAlertCheckService->addMessageAlertCheck($listReservation['reservation_id'], $listReservation['cp_id'], self::CHECKED);
            }
        }

        //Send Alert
        $this->sendAlert($listDeliveringReservations,$listDeliveredReservations,$listDeliveryFailReservations,$listSendMailFailCps);
    }

    private function getListReservations() {
        $sql = "SELECT R.id reservation_id,R.status status,C.id cp_id
                  FROM cp_message_delivery_reservations R
                 INNER JOIN cp_actions A ON R.cp_action_id = A.id AND A.del_flg = 0
                 INNER JOIN cp_action_groups G ON G.id = A.cp_action_group_id AND G.del_flg = 0
                 INNER JOIN cps C ON C.id = G.cp_id AND C.del_flg = 0
                 WHERE NOT EXISTS (SELECT CH.id
                                     FROM message_alert_checks CH
                                    WHERE CH.cp_message_delivery_reservation_id = R.id
                                      AND (    CH.checked = ". self::CHECKED ."
                                            OR CH.count >= ".self::MAX_SEND_COUNT."))
                   AND R.status >= ".CpMessageDeliveryReservation::STATUS_DELIVERING."
                   AND R.updated_at >= ( NOW() - INTERVAL 1 DAY )
                   AND R.del_flg = 0";
        $listReservations = $this->db->getBySQL($sql,array());
        return $listReservations;
    }

    private function getListDeliveringReservation($reservation_ids) {
        $sql = "SELECT DISTINCT R.id reservation_id,C.id cp_id
                  FROM cp_message_delivery_reservations R
                 INNER JOIN cp_actions A ON R.cp_action_id = A.id AND A.del_flg = 0
                 INNER JOIN cp_action_groups G ON G.id = A.cp_action_group_id AND G.del_flg = 0
                 INNER JOIN cps C ON C.id = G.cp_id AND C.del_flg = 0
                 WHERE R.status = ".CpMessageDeliveryReservation::STATUS_DELIVERING."
                   AND R.updated_at <= ( NOW() - INTERVAL 1 HOUR )
                   AND R.id IN (" . implode(',', $reservation_ids) . ")
                   AND R.del_flg = 0";
        $list_delivering = $this->db->getBySQL($sql,array());
        return $list_delivering;
    }

    private function getListDeliveredReservation($reservation_ids) {
        $sql = "SELECT DISTINCT R.id reservation_id,T.user_id user_id,C.id cp_id
                  FROM cp_message_delivery_reservations R
                 INNER JOIN cp_message_delivery_targets T ON R.id = T.cp_message_delivery_reservation_id AND T.status != ".CpMessageDeliveryTarget::STATUS_DELIVERED." AND T.del_flg = 0
                 INNER JOIN cp_actions A ON T.cp_action_id = A.id AND A.del_flg = 0
                 INNER JOIN cp_action_groups G ON G.id = A.cp_action_group_id AND G.del_flg = 0
                 INNER JOIN cps C ON C.id = G.cp_id AND C.del_flg = 0
                 INNER JOIN brands_users_relations RE ON RE.brand_id = C.brand_id AND RE.user_id = T.user_id AND RE.withdraw_flg = 0 AND RE.del_flg = 0
                 WHERE R.status = ".CpMessageDeliveryReservation::STATUS_DELIVERED."
                   AND R.id IN (" . implode(',', $reservation_ids) . ")
                   AND R.del_flg = 0";
        $list_delivered = $this->db->getBySQL($sql,array());
        $userService = new UserService();
        foreach($list_delivered as $key => $value) {
            $user = $userService->getUserByBrandcoUserId($value['user_id']);
            $monipla_user = \Monipla\Core\MoniplaCore::getInstance()->getUserByQuery(array(
                'class' => 'Thrift_UserQuery',
                'fields' => array(
                    'socialMediaType' => 'Platform',
                    'socialMediaAccountID' => $user->monipla_user_id,
                )));
            if ($monipla_user->result->status != Thrift_APIStatus::SUCCESS) {
                unset($list_delivered[$key]);
            }
        }
        return $list_delivered;
    }

    private function getListDeliveryFailReservation($reservation_ids) {
        $sql = "SELECT DISTINCT R.id reservation_id,C.id cp_id
                  FROM cp_message_delivery_reservations R
                 INNER JOIN cp_actions A ON R.cp_action_id = A.id AND A.del_flg = 0
                 INNER JOIN cp_action_groups G ON G.id = A.cp_action_group_id AND G.del_flg = 0
                 INNER JOIN cps C ON C.id = G.cp_id AND C.del_flg = 0
                 WHERE R.status = ".CpMessageDeliveryReservation::STATUS_DELIVERY_FAIL."
                   AND R.id IN (" . implode(',', $reservation_ids) . ")
                   AND R.del_flg = 0";
        $listDelivery_fail_reservations = $this->db->getBySQL($sql,array());
        return $listDelivery_fail_reservations;
    }

    private function getListSendMailFailCp() {
        $sql = "SELECT C.id cp_id
                  FROM cps C
                 INNER JOIN cp_action_groups G ON C.id = G.cp_id AND G.del_flg = 0
                 INNER JOIN cp_actions A ON A.cp_action_group_id = G.id AND A.type = 0 AND A.del_flg = 0
                 WHERE NOT EXISTS(SELECT CH.id
                                    FROM message_alert_checks CH
                                   WHERE CH.cp_id = C.id
                                     AND CH.cp_message_delivery_reservation_id = 0
                                     AND (   CH.count >= ".self::MAX_SEND_COUNT."
                                          OR CH.checked = ".self::CHECKED."))
                   AND C.send_mail_flg = ".Cp::FLAG_SHOW_VALUE."
                   AND C.status = ".Cp::CAMPAIGN_STATUS_OPEN."
                   AND C.start_date <= now() - INTERVAL 600 SECOND
                   AND (C.end_date >= now() OR C.permanent_flg = " . Cp::PERMANENT_FLG_ON . ")
                   AND C.del_flg = 0
                   AND NOT EXISTS(SELECT 1
                                    FROM cp_message_delivery_reservations R
                                   WHERE R.cp_action_id = A.id
                                     AND R.del_flg = 0
                                     AND R.status >= ".CpMessageDeliveryReservation::STATUS_SCHEDULED.")";
        $list_send_mail_fail_cps = $this->db->getBySQL($sql,array());
        return $list_send_mail_fail_cps;
    }

    private function sendAlert($delivering_list,$deliveried_list, $delivery_fail_list, $send_mail_fail_list) {

        if (!count($delivering_list) && !count($deliveried_list) && !count($delivery_fail_list) && !count($send_mail_fail_list)) return;

        //Send Mail
        $mailParams = array(
            'STATUS_DELIVERING' => $delivering_list,
            'STATUS_DELIVERY_FAIL' => $delivery_fail_list,
            'STATUS_DELIVERED' => $deliveried_list,
            'STATUS_SEND_MAIL' => $send_mail_fail_list,
        );
        $mail = new MailManager(array('FromAddress'=> 'bc-dev@aainc.co.jp'));
        $mail->loadMailContent('message_alert_check');
        $settings = aafwApplicationConfig::getInstance();
        $mailAddress = $settings->Mail['ALERT']['CcAddress'];
        $mail->sendNow($mailAddress, $mailParams);

        //Send Hipchat
        if ($delivering_list) {
            $this->hipchat_logger->error('■配送中で1時間以上経過しているメッセージ: ' . json_encode($delivering_list));
        }

        if ($deliveried_list) {
            $this->hipchat_logger->error('■配送完了で、このreservationに紐づくcp_message_delivery_targetsのstatusが0または2のメッセージ: ' . substr(json_encode($deliveried_list),1,300));
        }

        if ($delivery_fail_list) {
            $this->hipchat_logger->error('■配送失敗のメッセージ: ' . json_encode($delivery_fail_list));
        }

        if ($send_mail_fail_list) {
            $this->hipchat_logger->error('■開催時にメールが送信されていないキャンペーン: ' . json_encode($send_mail_fail_list));
        }
    }
}