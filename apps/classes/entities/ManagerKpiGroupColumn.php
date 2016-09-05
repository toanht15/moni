<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class ManagerKpiGroupColumn extends aafwEntityBase {
    protected $_Relations = array(
        'ManagerKpiColumns' => array(
            'manager_kpi_column_id' => 'id'
        )
    );
}
