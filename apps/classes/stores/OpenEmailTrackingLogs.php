<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityStoreBase' );
class OpenEmailTrackingLogs extends aafwEntityStoreBase {
    protected $_TableName = "open_email_tracking_logs";
    protected $_EntityName = "OpenEmailTrackingLog";

    public static $csv_header = array(
        'メッセージID',
        'メッセージタイトル',
        '種類',
        'アカウント名',
        'ブランドID',
        '送信日時',
        '送信数',
        '開封数',
        'BRANDCoへのアクセス数',
        '閲覧数',
        '閲覧完了数',
        '開封率',
        '閲覧到達率'
    );

    public static function writeCSVFile($cp_action_groups, $cp_flow_service, $brand, $cp, $csv) {

        try {
            $service_factory = new aafwServiceFactory();

            /** @var CpMessageDeliveryService $message_delivery_service */
            $message_delivery_service = $service_factory->create('CpMessageDeliveryService');

            $object = new aafwObject();
            $open_email_log_model = $object->getModel('OpenEmailTrackingLogs');
            $clicked_email_link_model = $object->getModel('ClickEmailLinkLogs');

            foreach ($cp_action_groups as $action_group) {
                $first_action = $cp_flow_service->getFirstActionInGroupByGroupId($action_group->id);
                $delivered_reservations = $message_delivery_service->getDeliveredCpMessageDeliveryReservationByCpActionId($first_action->id);

                if (!$delivered_reservations) {
                    continue;
                }

                $first_delivery = $delivered_reservations->current();

                //送付数を計算する
                $target_count = 0;
                foreach ($delivered_reservations as $delivered_reservation) {
                    if($delivered_reservation->delivery_type == CpMessageDeliveryReservation::DELIVERY_TYPE_NONE) {
                        continue;
                    }
                    $target_count += $message_delivery_service->getTargetsCountByReservationId($delivered_reservation->id);
                }

                if (!$target_count) {
                    continue;
                }

                //開封数を取得
                $opened_email_count = $open_email_log_model->count(array('cp_action_id' => $first_action->id));

                $thread_view_from_email = $clicked_email_link_model->getSum('click_count', array('cp_action_id' => $first_action->id));

                $cp_member_count = $first_action->getMemberCount();

                $data_csv = array();
                $data_csv[] = $first_action->id;
                $data_csv[] = $cp->getTitle();
                $data_csv[] = ($cp->type == Cp::TYPE_CAMPAIGN) ? 'キャンペーン' : 'メッセージ';
                $data_csv[] = $brand->name;
                $data_csv[] = $brand->directory_name;
                $data_csv[] = ($first_delivery->delivery_date == '0000-00-00 00:00:00') ? $first_delivery->updated_at : $first_delivery->delivery_date;
                $data_csv[] = $target_count;
                $data_csv[] = $opened_email_count;
                $data_csv[] = $thread_view_from_email ? $thread_view_from_email : '-';
                $data_csv[] = $cp_member_count[CpUserService::CACHE_TYPE_READ_PAGE];
                $data_csv[] = $cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION];
                $data_csv[] = ($opened_email_rate = $opened_email_count * 100 / $target_count) ? $opened_email_rate . '%' : '-';
                $data_csv[] = $cp_member_count[CpUserService::CACHE_TYPE_READ_PAGE] * 100 / $target_count . '%';

                $array_data = $csv->out(array('data' => $data_csv), 0);
                print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
            }

        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        }
    }
}
