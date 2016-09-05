<?php
AAFW::import('jp.aainc.classes.brandco.cp.CpCopyCreator');

class CopyCampaigns {

    const START_DATE_TIME = "10:00:00";
    const END_DATE_TIME = "9:59:59";
    const ANNOUNCE_DATE_TIME = "23:59:59";

    protected $argv;
    protected $service_factory;
    protected $cp_copy_creator;
    protected $logger;
    protected $execute_class;

    public function __construct($argv) {
        $this->argv = $argv;
        $this->service_factory = new aafwServiceFactory();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->execute_class = get_class($this);
        $this->cp_copy_creator = new CpCopyCreator();
    }

    public function doProcess() {
        try {
            $this->logger->warn("start batch: class=" . $this->execute_class);
            $start_time = date("Y-m-d H:i:s");
            $this->executeProcess();
            $end_time = date("Y-m-d H:i:s");
            $this->logger->warn($this->execute_class . ' Status:Success Start_Time:' . $start_time . ' End_Time:' . $end_time);

        } catch (Exception $e) {
            $end_time = date("Y-m-d H:i:s");
            $this->logger->warn($this->execute_class . ' Status:Error Start_Time:' . $start_time . ' End_Time:' . $end_time . ' Detail:' . $e);
        }
    }

    public function executeProcess() {
        if(count($this->argv)!= 3){
            echo "cp_idやstart_dateを入力してください! \n";
            return;
        }
        $cp_id = $this->argv[1];
        $list_start_date = $this->argv[2];

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->service_factory->create('CpFlowService');
        $cp = $cp_flow_service->getCpById($cp_id);

        if (!$cp) {
            echo "cp_idを正しく入力してください！\n";
            return;
        }

        $start_dates = explode(',', $list_start_date);
        if (!$this->checkStartDates($start_dates)) {
            echo "start_dateを正しく入力してください！\n";
            return;
        }

        $data = array();
        $data['cp'] = $cp;

        if ($data['cp']->restricted_address_flg == Cp::CP_RESTRICTED_ADDRESS_FLG_ON) {
            $data['restricted_addresses'] = $cp->getRestrictedAddresses();
        }

        foreach ($start_dates as $start_date) {
            list($data['start_date'], $data['end_date'], $data['announce_date']) = $this->createStartDateAndEndDate($start_date);

            $copy_cp = $this->cp_copy_creator->create($data);
            if (!$copy_cp) {
                echo "エラーが発生されましたので、コピーできませんでした！\n";
                return;
            }
        }
    }

    /**
     * @param $start_dates
     * @return bool
     */
    private function checkStartDates($start_dates) {
        foreach ($start_dates as $date) {
            if (strtotime($date) === false) {
                return false;
            }

            //check format
            list($year, $month, $day) = explode('/', $date);
            if (checkdate($month, $day, $year) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $start_date
     * @return array
     */
    private function createStartDateAndEndDate($start_date) {
        $start_date_time = strtotime($start_date . ' ' . self::START_DATE_TIME);
        $end_date_time = strtotime($start_date . ' ' . self::END_DATE_TIME);
        $announce_date_time = strtotime($start_date . ' ' . self::ANNOUNCE_DATE_TIME);

        $cp_start_date = date('Y/m/d H:i:s', $start_date_time);
        $cp_end_date = date('Y/m/d H:i:s', $end_date_time + 86400);
        $cp_announce_date = date('Y/m/d H:i:s', $announce_date_time + 86400);

        return array($cp_start_date, $cp_end_date, $cp_announce_date);
    }
}