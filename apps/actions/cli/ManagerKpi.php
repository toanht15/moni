<?php
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');

/**
 * Class ManagerKpi
 * php AAFW.php bat jp.aainc.actions.cli.ManagerKpi [date[d]=yyyy-mm-dd] [c=class name[,class name]] [since=yyyy-mm-dd until=yyyy-mm-dd]
 */
class ManagerKpi extends aafwGETActionBase {

    private $class;
    private $date;
    private $since;
    private $until;
    private $logger;

    public function validate () {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->logger->info('ManagerKpi#validate start.');

        if(isset($this->REQUEST['date']) && !$this->isDateTime($this->REQUEST['date'])) {
            echo 'Invalid argument:date';
            exit;
        }
        if(isset($this->REQUEST['d']) && !$this->isDateTime($this->REQUEST['d'])) {
            echo 'Invalid argument:d';
            exit;
        }
        if(isset($this->REQUEST['since']) || isset($this->REQUEST['until'])) {
            if(isset($this->REQUEST['since']) && !$this->isDateTime($this->REQUEST['since'])) {
                echo 'Invalid argument:since';
                exit;
            }
            if(isset($this->REQUEST['until']) && !$this->isDateTime($this->REQUEST['until'])) {
                echo 'Invalid argument:until';
                exit;
            }
            if(date($this->REQUEST['until']) > date('Y-m-d', strtotime('-1 day'))) {
                echo 'Too much date:until';
                exit;
            }
            if(!$this->REQUEST['since'] || !$this->REQUEST['until']) {
                echo 'Please set [since && until] arguments.';
                exit;
            }
        }
        $this->logger->info('ManagerKpi#validate end.');
        return true;
    }

    function doAction() {
        $this->logger->info('ManagerKpi#doAction start.');

        if($this->REQUEST['date'] || $this->REQUEST['d']) {
            $this->date = $this->REQUEST['date'];

        } else{
            $this->date = date('Y-m-d', strtotime('-1 day'));



        }
        $this->since = $this->REQUEST['since'];

        $this->until = $this->REQUEST['until'];

        $this->class = $this->REQUEST['c'];

        try {

            $this->logger->info("ManagerKpi#doAction start2");

            $this->setServiceFactory(new aafwServiceFactory ());

            /** @var ManagerKpiService $manager_kpi_service */
            $manager_kpi_service = $this->createService('ManagerKpiService');
            if ($this->class) {
                $columnNames = explode(',', $this->class);

                $columns = array();
                foreach ($columnNames as $columnName) {
                    if (!preg_match("/^jp.aainc/", $columnName)) {
                        $columnName = 'jp.aainc.classes.manager_kpi.' . $columnName;
                    }
                    $column = $manager_kpi_service->getColumn(array('import' => $columnName));
                    if (!$column) continue;
                    $columns[] = $column;
                }
            } else {
                $columns = $manager_kpi_service->getColumns();
            }

            if(!$columns) {
                return;
            }
            // KPIバッチが落ちることの暫定対処で、うまくいかなかった場合はリトライしている。根本解決ができた場合は不要になる。
            $columns_array = $columns->toArray();
            $last_column = end($columns_array); // KPIカラムの最後の値が入力されているか判定
            if($manager_kpi_service->getManagerKpiValueByColumnIdAndDate($last_column->id, $this->date)) {
                return;
            }

            foreach ($columns as $column) {
                if (!$column->import) continue;
                if ($this->since && $this->until) {
                    $start = strtotime($this->since);
                    $end = strtotime($this->until);
                    while ($start < $end) {
                        $date = date('Y-m-d', $start);

                        $value = $manager_kpi_service->doExecute($column->import, $date);
                        $manager_kpi_service->setValueByColumnIdAndDate($column->id, $date, $value);

                        $manager_kpi_service->setDateStatus($date, ManagerKpiService::DATE_STATUS_FINISH);

                        $start = strtotime("+1 day", $start);
                    }
                } else {
                    $value = $manager_kpi_service->doExecute($column->import, $this->date);
                    $manager_kpi_service->setValueByColumnIdAndDate($column->id, $this->date, $value);

                    $manager_kpi_service->setDateStatus($this->date, ManagerKpiService::DATE_STATUS_FINISH);
                }
            }
        } catch (Exception $e) {
            $this->logger->error('MangerKpi batch error.' . $e);
        }

        $this->logger->info("ManagerKpi#doAction end");

        echo "finish\n";
    }
}
