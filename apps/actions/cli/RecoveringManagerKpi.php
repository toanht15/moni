<?php
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');

class RecoveringManagerKpi extends aafwGETActionBase {

    const MAX_DAY = 100;

    public function validate () {
        return true;
    }

    function doAction() {
        $logger = aafwLog4phpLogger::getDefaultLogger();
        $logger->info('RecoveringManagerKpi#doAction start.');

        try {
            $this->setServiceFactory(new aafwServiceFactory ());

            /** @var ManagerKpiService $manager_kpi_service */
            $manager_kpi_service = $this->createService('ManagerKpiService');
            $columns = $manager_kpi_service->getColumns();

            if(!$columns) { return; }

            for ($i = 2; $i <= self::MAX_DAY; $i++) {
                $date = date('Y-m-d', strtotime('-' . $i . ' day'));

                foreach ($columns as $column) {
                    if (!$column->import) continue;

                    if ($manager_kpi_service->getManagerKpiValueByColumnIdAndDate($column->id, $date)) continue;

                    $logger->info("RecoveringManagerKpi#doAction starting KPI: " . $column->name . ' - date: ' . $date);

                    $value = $manager_kpi_service->doExecute($column->import, $date);
                    $manager_kpi_service->setValueByColumnIdAndDate($column->id, $date, $value);

                    $manager_kpi_service->setDateStatus($date, ManagerKpiService::DATE_STATUS_FINISH);

                    $logger->info("RecoveringManagerKpi#doAction ending KPI: " . $column->name . ' - date: ' . $date);
                }

            }
        } catch (Exception $e) {
            $logger->error('RecoveringManagerKpi batch error.' . $e);
        }

        $logger->info("RecoveringManagerKpi#doAction end");

        echo "finish\n";
    }
}
