<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );

class ManagerKpiColumn extends aafwEntityBase {

    /** @var ManagerKpiService manager_kpi_service */
    protected $manager_kpi_service;

    protected $_Relations = array(
       'ManagerKpiValues' => array(
           'id' => 'column_id'
       )
    );

    public function getValueByDate($date) {

        if(!$this->manager_kpi_service) {
            $serviceFactory = new aafwServiceFactory ();
            $this->manager_kpi_service = $serviceFactory->create('ManagerKpiService');
        }

        return $this->manager_kpi_service->getValueByColumnIdAndDate($this->id, $date);
    }

    /**
     * 数値表記と小数点のある値は第二位までにフォーマットし取得
     * @param $date
     * @param $column_import
     * @return string
     */
    public function getFormattedValueByDate($date, $column_import) {
        $filter = array(
            'summed_date' => $date
        );
        $result = $this->getManagerKpiValue($filter);

        if (in_array($column_import->import, ManagerKpiColumns::$float_colomns)){
            $formatted_value = number_format($result->value, 2);
        }else{
            $formatted_value = number_format($result->value);
        }
        return $formatted_value;
    }
}
