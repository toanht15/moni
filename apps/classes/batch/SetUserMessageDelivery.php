<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

class SetUserMessageDelivery extends BrandcoBatchBase {

    /** @var  CpFlowService */
    private $cp_flow_service;

    public function __construct($argv) {
        parent::__construct($argv);
        $this->cp_flow_service = $this->service_factory->create("CpFlowService");
    }

    public function executeProcess() {
        if (!$this->argv || count($this->argv) !== 4) {
            echo "brand_id, start_date, end_date, delivery_dateを入力してください！\n";
            return;
        }

        $brand_id = $this->argv['brand_id'];

        $start_date = $this->convertDate($this->argv['start_date']);
        if (!$start_date) {
            echo "start_date invalid! 「月/日」フォーマットで再入力してください！\n";
            return;
        }

        $end_date = $this->convertDate($this->argv['end_date'], "23:59:59");
        if (!$end_date) {
            echo "end_date invalid! 「月/日」フォーマットで再入力してください！\n";
            return;
        }

        $delivery_date = $this->getDeliveryDate($this->argv['delivery_date']);

        if (!$delivery_date) {
            echo "delivery_date invalid! 「月/日,時:分」フォーマットで再入力してください！\n";
            return;
        }

        if (strtotime($delivery_date) - strtotime($end_date) < 0) {
            echo "delivery_dateはend_dateの過去に出来ません！再入力してください！\n";
            return;
        }

        try {
            $campaigns = $this->cp_flow_service->getCpsByBrandIdAndPeriod($brand_id, $start_date, $end_date);
            if (Util::isNullOrEmpty($campaigns) || count($campaigns) == 0) {
                echo "キャンペーンがありません！条件は確認してください！\n";
            }

            foreach ($campaigns as $cp) {
                $this->setUserMessageDelivery($cp->id, $delivery_date);
            }
        } catch (Exception $e) {
            $this->logger->error("SetUserMessageDelivery#executeProcess error! ");
            $this->logger->error($e);
        }
    }

    /**
     * @param $input_date
     * @param null $input_time
     * @return bool|null|string
     */
    private function convertDate($input_date, $input_time = null) {
        $date = $input_date." ".$input_time;
        $time_stamp = strtotime($date);

        if (!$time_stamp) {
            return null;
        }
        $output_date = date("Y-m-d H:i:s", $time_stamp);

        return $output_date;
    }

    /**
     * @param $input_date
     * @return bool|null|string
     */
    private function getDeliveryDate($input_date) {
        $delivery_date = explode(",", $input_date);

        $date = $delivery_date[0];
        $time = $delivery_date[1] ?: "00:00";

        return $this->convertDate($date, $time);
    }

    /**
     * @param $cp_id
     * @param $delivery_date
     */
    private function setUserMessageDelivery($cp_id, $delivery_date) {
        $cp_announce_id = $this->cp_flow_service->getCpActionIdsByCpIdAndType($cp_id, CpAction::TYPE_ANNOUNCE)[0];

        /** @var CpMessageDeliveryService $cp_message_delivery_service */
        $cp_message_delivery_service = $this->service_factory->create("CpMessageDeliveryService");
        $reservation = $cp_message_delivery_service->getCurrentCpMessageDeliveryReservationByCpActionId($cp_announce_id);

        if ($reservation && $reservation->status == CpMessageDeliveryReservation::STATUS_FIX) {
            $reservation->delivery_date = $delivery_date;
            $reservation->status = CpMessageDeliveryReservation::STATUS_SCHEDULED;
            $cp_message_delivery_service->updateCpMessageDeliveryReservation($reservation);
        }
    }
}