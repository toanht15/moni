<?php
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
AAFW::import('jp.aainc.classes.services.ManagerKpiService');

/**
 * Class ManagerBrandKpi
 * php AAFW.php bat jp.aainc.actions.cli.ManagerBrandKpi [date[d]=yyyy-mm-dd] [c=class name[,class name]] [since=yyyy-mm-dd until=yyyy-mm-dd]
 */
class ManagerBrandKpi extends aafwGETActionBase {

    private $class;
    private $date;
    private $since;
    private $until;

    public function validate () {

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

        return true;
    }

    function doAction() {

        if($this->REQUEST['date'] || $this->REQUEST['d']) {
            $this->date = $this->REQUEST['date'];
        } else{
            $this->date = date('Y-m-d', strtotime('-1 day'));
        }
        $this->since = $this->REQUEST['since'];
        $this->until = $this->REQUEST['until'];
        $this->class = $this->REQUEST['c'];

        $this->logger = aafwLog4phpLogger::getDefaultLogger();

        $this->logger->info("ManagerBrandKpi Start");

        $this->setServiceFactory(new aafwServiceFactory ());

        try {

            /** @var ManagerBrandKpiService $manager_kpi_service */
            $manager_kpi_service = $this->createService('ManagerBrandKpiService');
            if ($this->class) {
                $columnNames = explode(',', $this->class);

                $columns = array();
                foreach ($columnNames as $columnName) {
                    if (!preg_match("/^jp.aainc/", $columnName)) {
                        $columnName = 'jp.aainc.classes.manager_brand_kpi.' . $columnName;
                    }
                    $column = $manager_kpi_service->getColumn(array('import' => $columnName));
                    if (!$column) continue;
                    $columns[] = $column;
                }
            } else {
                $columns = $manager_kpi_service->getColumns();
            }

            /** @var BrandService $brand_service */
            $brand_service = $this->createService('BrandService');
            foreach ($brand_service->getAllBrands() as $brand) {
                foreach ($columns as $column) {
                    if (!$column->import) continue;
                    if ($this->since && $this->until) {
                        $start = strtotime($this->since);
                        $end = strtotime($this->until);
                        while ($start < $end) {
                            $date = date('Y-m-d', $start);

                            $value = $manager_kpi_service->doExecute($column->import, $date, $brand->id);
                            $manager_kpi_service->setValue($column->id, $brand->id, $date, $value);

                            $manager_kpi_service->setDateStatus($date, ManagerKpiService::DATE_STATUS_FINISH);

                            $start = strtotime("+1 day", $start);
                        }
                    } else {
                        $value = $manager_kpi_service->doExecute($column->import, $this->date, $brand->id);
                        $manager_kpi_service->setValue($column->id, $brand->id, $this->date, $value);

                        $manager_kpi_service->setDateStatus($this->date, ManagerKpiService::DATE_STATUS_FINISH);
                    }
                }
            }
        } catch (Exception $e) {
            $this->logger->error('MangerBrandKpi batch error.' . $e);
        }

        $this->logger->info("ManagerBrandKpi End");

        echo "finish\n";
    }
}
