<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class ManagerKpiColumns extends aafwEntityStoreBase {

    const USER_PANEL_CLICK_NUM = 'jp.aainc.classes.manager_kpi.UserPanelClickNumKPI';

    public static $float_colomns = array(
        'jp.aainc.classes.manager_kpi.GaPageviewsPerSession'
    );

    protected $_TableName = 'manager_kpi_columns';
    protected $_EntityName = 'ManagerKpiColumn';
}