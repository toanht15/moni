<?php
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.services.base.KpiServiceBase');

class ManagerKpiService extends KpiServiceBase {

    const ALL_CSV_DOWNLOAD = 0; //ALL Kpi CSV Download
    const GET_CSV_BY_DATE = 1; // GET CSV BY Date

    public function __construct() {
        $this->manager_kpi_column = $this->getModel('ManagerKpiColumns');
        $this->manager_kpi_value = $this->getModel('ManagerKpiValues');
        $this->manager_kpi_date = $this->getModel('ManagerKpiDates');
    }

    public function setDateStatus($date, $status = self::DATE_STATUS_FINISH) {
        $manager_kpi_date = $this->manager_kpi_date->findOne(array('summed_date' => $date));
        $manager_kpi_date->summed_date = $date;
        $manager_kpi_date->status = $status;

        return $this->manager_kpi_date->save($manager_kpi_date);
    }

    public function getManagerKpiColumnByImport($import) {
        $conditions = array(
            'import' => $import,
        );

        return $this->manager_kpi_column->findone($conditions);
    }

    public function getManagerKpiValueByColumnIdAndDate($manager_kpi_column_id, $date) {
        $conditions = array(
            'column_id' => $manager_kpi_column_id,
            'summed_date' => $date,
        );
        return $this->manager_kpi_value->findOne($conditions);
    }

    public function getValueByColumnIdAndDate($manager_kpi_column, $date) {
        $conditions = array(
            'column_id' => $manager_kpi_column->id,
            'summed_date' => $date,
        );

        $result = $this->manager_kpi_value->findOne($conditions);

        if (in_array($manager_kpi_column->import, ManagerKpiColumns::$float_colomns)) {
            $formatted_value = number_format($result->value, 2);
        } else {
            $formatted_value = number_format($result->value);
        }

        return $formatted_value;
    }

    public function setValueByColumnIdAndDate($columnId, $date, $value) {
        $manager_kpi_value = $this->getManagerKpiValueByColumnIdAndDate($columnId, $date);
        $manager_kpi_value->column_id = $columnId;
        $manager_kpi_value->summed_date = $date;
        $manager_kpi_value->value = $value;

        return $this->manager_kpi_value->save($manager_kpi_value);
    }

    public function doExecute($path, $date) {
        list($class) = AAFW::import($path);
        $kpi = new $class;

        return $kpi->doExecute($date);
    }

    public function getGuinness($columns) {
        $records = array();
        foreach ($columns as $column) {
            $filter = array(
                'conditions' => array(
                    'column_id' => $column->id
                )
            );
            if (in_array($column->import, ManagerKpiColumns::$float_colomns)) {
                $records[$column->id] = number_format($this->manager_kpi_value->getMax('value', $filter), 2);
            } else {
                $records[$column->id] = number_format($this->manager_kpi_value->getMax('value', $filter));
            }
        }
        return $records;
    }

    public function getManagerKpiValuesByDate($date) {
        $filter = array(
            'summed_date' => $date
        );
        return $this->manager_kpi_value->find($filter);
    }

    public function getImportById($id) {
        $filter = array(
            'id' => $id
        );
        return $this->manager_kpi_column->findOne($filter);
    }

    public function getManagerKpiDatesByFromDateAndToDate($from_date, $to_date, $order = 'summed_date') {
        $filter = array(
            'conditions' => array(
            ),
            'order' => $order
        );

        if ($from_date) {
            $filter['conditions']['summed_date:>='] = date('Y-m-d', strtotime($from_date));
        }

        if ($to_date) {
            $filter['conditions']['summed_date:<='] = date('Y-m-d', strtotime($to_date));
        }

        return $this->manager_kpi_date->find($filter);
    }

    public function getManagerKpiByDate($date) {
        $kpi_values = array();
        $get_brand_kpi_values = $this->getManagerKpiValuesByDate($date->summed_date);
        $kpi_values[] = $date->summed_date;
        foreach ($get_brand_kpi_values as $value) {
            $import = $this->getImportById($value->column_id)->import;
            if (in_array($import, ManagerKpiColumns::$float_colomns)) {
                $kpi_values[] = floatval($value->value);
            } else {
                $kpi_values[] = intval($value->value);
            }

        }
        return $kpi_values;
    }
}