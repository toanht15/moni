<?php
require_once dirname(__FILE__) . '/../../config/define.php';
/**
 * Class DailyAlertMessage
 * php DailyAlertMessage.php
 */
class DailyAlertMessage {

    private $logger;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
        $this->service_factory = new aafwServiceFactory();
    }

    public function doProcess() {

        $this->logger->info("DailyAlertMessage Start");

        try {
            $dates = array(
                $today = date("Y-m-d"),
                $tomorrow = date('Y-m-d', strtotime($today . '+1 day')),
                $two_day_after = date('Y-m-d', strtotime($today . '+2 day')),
                $three_day_after = date('Y-m-d', strtotime($today . '+3 day')),
                $four_day_after = date('Y-m-d', strtotime($today . '+4 day')),
                $five_day_after = date('Y-m-d', strtotime($today . '+5 day')),
                $six_day_after = date('Y-m-d', strtotime($today . '+6 day')),
                $seven_day_after = date('Y-m-d', strtotime($today . '+7 day'))
            );

            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $this->service_factory->create('CpFlowService');
            /** @var BrandService $brand_service */
            $brand_service = $this->service_factory->create('BrandService');
            /** @var CpMessageDeliveryService $cp_message_delivery_service */
            $cp_message_delivery_service = $this->service_factory->create('CpMessageDeliveryService');
            /** @var BrandPageSettingService $brand_page_setting_service */
            $brand_page_setting_service = $this->service_factory->create('BrandPageSettingService');
            /** @var CpAlertMailService $cp_alert_mail_service */
            $cp_alert_mail_service = $this->service_factory->create('CpAlertMailService');

            $cps_expired_announce_date = $cp_flow_service->getExpireAnnounceDateCps();
            foreach ($cps_expired_announce_date as $cp) {
                $check_passed_flg = $cp_alert_mail_service->getCpAlertMailsByCpId($cp->id);
                if($check_passed_flg->passed_announce_flg == CpAlertMailService::PASSED_ANNOUNCE_FLG_UNSUBSCRIBE){
                    continue;
                }
                $brand = $brand_service->getBrandById($cp->brand_id);
                if ($brand == null) {
                    continue;
                }
                $brand_public_status = $brand_page_setting_service->getPageSettingsByBrandId($cp->brand_id)->public_flg;
                if ($brand->test_page == BrandService::TEST || $brand_public_status == BrandService::NONOPEN) {
                    continue;
                }
                $cp_action_groups = $cp_flow_service->getCpActionGroupsByCpId($cp->id);
                foreach ($cp_action_groups as $cp_action_group) {
                    if ($cp_action_group->order_no == 1) {
                        continue;
                    }
                    $check_type_announce_exist = $cp_flow_service->getCpActionByCpActionGroupIdAndType($cp_action_group->id, CpAction::TYPE_ANNOUNCE);
                    if (!$check_type_announce_exist) {
                        continue;
                    }
                    $cp_action = $cp_flow_service->getCpActionByGroupIdAndOrderNo($cp_action_group->id, 1);
                    $cp_message_delivery_reservations = $cp_message_delivery_service->getDeliveredCpMessageDeliveryReservationByCpActionId($cp_action->id);
                    if ($cp_message_delivery_reservations) {
                        continue;
                    }
                    $non_announce_cp = array(
                        'ID' => $cp->id,
                        'TITLE' => $cp->getTitle(),
                        'BRAND_NAME' => $brand->name,
                        'CAMPAIGN_URL' => $cp->getUrl(),
                    );
                    $this->Data['non_announce_cp'][] = $non_announce_cp;
                    break;
                }
            }

            foreach ($dates as $date) {
                $cps = $cp_flow_service->getAllCpsBySameAnnounceDate($date);
                foreach ($cps as $cp) {
                    if($cp == null){
                        continue;
                    }
                    $check_passed_flg = $cp_alert_mail_service->getCpAlertMailsByCpId($cp->id);
                    if($check_passed_flg->now_announce_flg == CpAlertMailService::NOW_ANNOUNCE_FLG_UNSUBSCRIBE){
                        continue;
                    }
                    $brand = $brand_service->getBrandById($cp->brand_id);
                    if ($brand == null) {
                        continue;
                    }
                    $brand_public_status = $brand_page_setting_service->getPageSettingsByBrandId($cp->brand_id)->public_flg;
                    if ($brand->test_page == BrandService::TEST || $brand_public_status == BrandService::NONOPEN) {
                        continue;
                    }
                    $announcement_waiting_info = array(
                        'ID' => $cp->id,
                        'TITLE' => $cp->getTitle(),
                        'BRAND_NAME' => $brand_service->getBrandById($cp->brand_id)->name,
                        'CAMPAIGN_URL' => $cp->getUrl(),
                    );
                    if ($today == date("Y-m-d", strtotime($cp->announce_date))) {
                        $today_data[] = $announcement_waiting_info;
                    } elseif ($tomorrow == date("Y-m-d", strtotime($cp->announce_date))) {
                        $tomorrow_data[] = $announcement_waiting_info;
                    } elseif ($two_day_after == date("Y-m-d", strtotime($cp->announce_date))) {
                        $two_day_after_data[] = $announcement_waiting_info;
                    } elseif ($three_day_after == date("Y-m-d", strtotime($cp->announce_date))) {
                        $three_day_after_data[] = $announcement_waiting_info;
                    } elseif ($four_day_after == date("Y-m-d", strtotime($cp->announce_date))) {
                        $four_day_after_data[] = $announcement_waiting_info;
                    } elseif ($five_day_after == date("Y-m-d", strtotime($cp->announce_date))) {
                        $five_day_after_data[] = $announcement_waiting_info;
                    } elseif ($six_day_after == date("Y-m-d", strtotime($cp->announce_date))) {
                        $six_day_after_data[] = $announcement_waiting_info;
                    } elseif ($seven_day_after == date("Y-m-d", strtotime($cp->announce_date))) {
                        $seven_day_after_data[] = $announcement_waiting_info;
                    }
                }
            }

            $daily_alert_message_contents = array(
                'TODAY' => $today,
                'TODAY_DATA' => $today_data,
                'TOMORROW' => $tomorrow,
                'TOMORROW_DATA' => $tomorrow_data,
                'TWO_DAY_AFTER' => $two_day_after,
                'TWO_DAY_AFTER_DATA' => $two_day_after_data,
                'THREE_DAY_AFTER' => $three_day_after,
                'THREE_DAY_AFTER_DATA' => $three_day_after_data,
                'FOUR_DAY_AFTER' => $four_day_after,
                'FOUR_DAY_AFTER_DATA' => $four_day_after_data,
                'FIVE_DAY_AFTER' => $five_day_after,
                'FIVE_DAY_AFTER_DATA' => $five_day_after_data,
                'SIX_DAY_AFTER' => $six_day_after,
                'SIX_DAY_AFTER_DATA' => $six_day_after_data,
                'SEVEN_DAY_AFTER' => $seven_day_after,
                'SEVEN_DAY_AFTER_DATA' => $seven_day_after_data,
                'NON_ANNOUNCE_DATA' => $this->Data['non_announce_cp'],
            );

            $mailManager = new MailManager();
            $mailManager->loadMailContent('daily_alert_message');
            $settings = aafwApplicationConfig::getInstance();
            $mail_address = $settings->Mail['ALERT']['ToAddress'];
            $cc_mail_address = $settings->Mail['ALERT']['CcAddress'];
            $mailManager->sendNow($mail_address, $daily_alert_message_contents, $cc_mail_address);
            $this->logger->info("DailyAlertMessage End");

        } catch (Exception $e) {
            $this->logger->error('DailyAlertMessage batch error.' . $e);
        }
    }
}