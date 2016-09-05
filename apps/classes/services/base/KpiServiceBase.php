<?php
class KpiServiceBase extends aafwServiceBase {
    protected $manager_kpi_column;
    protected $manager_kpi_value;
    protected $manager_kpi_date;

    const DATE_STATUS_FINISH = 1;

    public function getColumn($filter) {
        return $this->manager_kpi_column->findOne($filter);
    }

    public function getColumns() {
        return $this->manager_kpi_column->findAll();
    }

    public function getDates($page = 1, $limit = 20, $params = array(), $order = 'summed_date DESC') {
        $filter = array(
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            'order' => $order,
        );

        return $this->manager_kpi_date->find($filter);
    }

    public function getDatesCount($page = 1, $limit = 20, $params = array()) {
        $filter = array(
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
        );
        return $this->manager_kpi_date->count($filter);
    }
}