<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.aafw.web.aafwController');
AAFW::import('jp.aainc.classes.services.ManagerKpiService');

/**
 * Class DailyManagerKpi
 * php DailyManagerKpi.php
 */
class DailyManagerKpi {

    private $logger;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
        $this->service_factory = new aafwServiceFactory();
    }


    public function doProcess($argv) {
        $isTestMode = $argv[1] !== null;

        $this->logger->info("DailyManagerKpi Start");

        try {
            /** @var ManagerKpiService $manager_kpi_service */
            $manager_kpi_service = $this->service_factory->create('ManagerKpiService');
            /** @var BrandService $brand_service */
            $brand_service = $this->service_factory->create('BrandService');
            /** @var ManagerBrandKpiService $manager_brand_service */
            $manager_brand_service = $this->service_factory->create('ManagerBrandKpiService');
            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $this->service_factory->create('CpFlowService');
            $today = date("Y/m/d");
            $yesterday = date('Y-m-d', strtotime($today . '-1 day'));

            $brands = $brand_service->getAllPublicBrand();
            $items = [];

            $manager_brand_kpi_values = $manager_brand_service->getManagerBrandKpiValuesByDate($yesterday);
            if (!count($manager_brand_kpi_values)) {
                $this->hipchat_logger->error('Brand KPI が取得できませんでした。');
                return;
            }

            $manager_base_url = config('Domain.brandco_manager');
            foreach ($brands as $brand) {
                $import = $manager_brand_service->getManagerBrandKpiColumnByImport('jp.aainc.classes.manager_brand_kpi.BrandsUsersNum');
                $cp_total = $cp_flow_service->getOpenCpsByBrandId($brand->id);
                $total = 0;
                $count_cp = [];
                $cp_ids = [];
                if(!empty($cp_total)){
                   foreach($cp_total as $val){
                       $cp_ids[] = $val->id;
                   }
                   if(!empty($cp_ids)){
                       foreach($cp_ids as $cp_id){
                        $cp_action_id = $cp_flow_service->getFirstActionOfCp($cp_id)->id;
                        $count_cp[] = $this->getUsersCountByCpActionId($cp_action_id);
                       }
                   }
                    $total = array_sum($count_cp);
                }

                $kpi_total_values = $manager_brand_service->getValue($import->id, $brand->id, date('Y-m-d', strtotime('yesterday')))->value;
                $old_total_values = $manager_brand_service->getValue($import->id, $brand->id, date('Y-m-d', strtotime('-2 days')))->value;
                $kpi_values_change = $kpi_total_values - $old_total_values;
                if ($kpi_values_change > 0) {
                    $color = "<td bgcolor=" . "green" . ">";
                    $symnbol = "+";
                } elseif ($kpi_values_change < 0) {
                    $color = "<td bgcolor=" . "red" . ">";
                    $symnbol = "";
                }
                $items[] = array(
                    'ID' => $brand->id,
                    'NAME' => $brand->name,
                    'TOTAL_USER' => $total,
                    'TOTAL' => $kpi_total_values,
                    'CHANGE' => $kpi_values_change,
                    'BRAND_URL' => "<a href=" . $brand->getUrl() . ">",
                    'BRAND_MANAGER_URL' => "<a href=" . Util::rewriteUrl('/dashboard', 'brand_kpi', array($brand->id), array(), $manager_base_url) . " >",
                    'COLOR' => $color,
                    'SYMBOL' => $symnbol
                );
            }
            foreach ($items as $key => $row) {
                $change[$key] = $row['CHANGE'];
            }

            array_multisort($change, SORT_DESC, $items);
            $manager_kpi_columns = $manager_kpi_service->getColumns();
            $manager_kpi_values = $manager_kpi_service->getManagerKpiValuesByDate($yesterday);

            if (!count($manager_kpi_values)) {
                $this->hipchat_logger->error('KPI が取得できませんでした。');
                return;
            }

            foreach ($manager_kpi_columns as $manager_kpi_column) {
                $kpi[] = array(
                    'KPI_NAME' => $manager_kpi_column->name,
                    'KPI_VALUE' => $manager_kpi_service->getValueByColumnIdAndDate($manager_kpi_column, $yesterday),
                );
            }

            $daily_manager_contents = array(
                'TODAY' => $today,
                'YESTERDAY' => $yesterday,
                'DATA' => $items,
                'KPI' => $kpi,
                'KPI_URL' => "<a href=" . Util::rewriteUrl('/dashboard', 'kpi', array(), array(), $manager_base_url) . " >",

            );

            $mailManager = new MailManager();
            $mailManager->loadMailContent('daily_manager_kpi');
            $settings = aafwApplicationConfig::getInstance();

            $mail_address = $settings->Mail['KPI']['ToAddress'];
            if ($isTestMode) {
                $mail_address = $settings->Mail['KPI']['ToTestAddress'];
                $mailManager->Subject = "KPIテスト用";
            }
            $cc_mail_address = $settings->Mail['KPI']['CcAddress'];
            $mailManager->sendNow($mail_address, $daily_manager_contents, $cc_mail_address);
            $this->logger->info("DailyManagerKpi End");

        } catch (Exception $e) {
            $this->logger->error('DailyManagerKpi batch error.' . $e);
        }
    }
    public function getUsersCountByCpActionId($cp_action_id) {
        $data_builder = new aafwDataBuilder();
        $date = new DateTime();
        $yesterday = $date->modify('-1 days');
        $start_date = $yesterday->format('Y-m-d 00:00:00');
        $end_date = $yesterday->format('Y-m-d 23:59:59');
        $result = $data_builder->getBySQL("SELECT COUNT(cpusr.id) FROM cp_user_action_statuses cpusr
                    WHERE cpusr.cp_action_id = " . $cp_action_id . " AND cpusr.del_flg = 0 AND cpusr.status = 1 AND cpusr.updated_at >= '".$start_date."' AND cpusr.updated_at <= '".$end_date."'", array());
        $shipping_count = (int)$result[0]['COUNT(cpusr.id)'];
        return $shipping_count;
    }
}
