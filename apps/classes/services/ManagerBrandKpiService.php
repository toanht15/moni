<?php
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.services.base.KpiServiceBase');

class ManagerBrandKpiService extends KpiServiceBase {

	public function __construct() {
		$this->manager_kpi_column = $this->getModel('ManagerBrandKpiColumns');
        $this->manager_kpi_value = $this->getModel('ManagerBrandKpiValues');
        $this->manager_kpi_date = $this->getModel('ManagerKpiDates');
	}

    public function setDateStatus($date, $status = self::DATE_STATUS_FINISH) {
        $manager_kpi_date = $this->manager_kpi_date->findOne(array('summed_date' => $date));
        $manager_kpi_date->summed_date = $date;
        $manager_kpi_date->brand_kpi_status = $status;

        return $this->manager_kpi_date->save($manager_kpi_date);
    }

    public function getAscendingSummedDates($order = 'summed_date') {
        $filter = array(
            'order' => $order,
        );
        return $this->manager_kpi_date->find($filter);
    }

    public function getManagerBrandKpiColumnByImport($import) {
        $filter = array(
            'import' => $import,
        );

        return $this->manager_kpi_column->findOne($filter);
    }

    public function getAllBrandValuesByBrandIdAndSummeddate($brandId, $date) {
        $conditions = array(
            'brand_id' => $brandId,
            'summed_date' => $date,
        );
        return $this->manager_kpi_value->find($conditions);
    }

    public function getValue($columnId, $brandId, $date) {
        $conditions = array(
            'column_id' => $columnId,
            'brand_id' => $brandId,
            'summed_date' => $date,
        );

        return $this->manager_kpi_value->findOne($conditions);
    }

    public function getTotalValue($columnId, $brandId) {
        $conditions = array(
            'column_id' => $columnId,
            'brand_id' => $brandId,
        );
        $manager_kpi_values = $this->manager_kpi_value->find($conditions);
        $total = array();
        foreach($manager_kpi_values as $manager_kpi_value){
            $total[] = $manager_kpi_value->value;

        }
        $sum_cp_users = array_sum($total);
        return $sum_cp_users;
    }

    public function setValue($columnId, $brandId, $date, $value) {
        $manager_kpi_value = $this->getValue($columnId, $brandId, $date);
        $manager_kpi_value->column_id = $columnId;
        $manager_kpi_value->brand_id = $brandId;
        $manager_kpi_value->summed_date = $date;
        $manager_kpi_value->value = $value;

        return $this->manager_kpi_value->save($manager_kpi_value);
    }

    public function doExecute($path, $date, $brandId) {
        list($class) = AAFW::import($path);
        $kpi = new $class;

        return $kpi->doExecute($date, $brandId);
    }

    public function getBrandKpiGuinness($columns, $brand_id) {
        $guinness = array();
        foreach ($columns as $column) {
            $filter = array(
                'conditions' => array(
                    'column_id' => $column->id,
                    'brand_id' => $brand_id
                )
            );
            $guinness[$column->id] = $this->manager_kpi_value->getMax('value', $filter);
        }
        return $guinness;
    }

    public function getManagerBrandKpiValuesByDate($summed_date){
        $filter = array(
            'summed_date' => $summed_date
        );
        return $this->manager_kpi_value->find($filter);
    }
}