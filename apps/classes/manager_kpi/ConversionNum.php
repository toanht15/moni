<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');

class ConversionNum implements IManagerKPI {

    function doExecute($date) {
        $brandUserConversion = aafwEntityStoreFactory::create('BrandsUsersConversions');
        $filter = array(
            'date_conversion:<' => date('Y-m-d', strtotime($date . '+1 day')),
            'date_conversion:>=' => date('Y-m-d', strtotime($date)),
        );
        return $brandUserConversion->count($filter);
    }

}
