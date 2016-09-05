<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.entities.Cp');

class MessageNumKPI implements IManagerKPI {

    function doExecute() {
        list($date, $brandId) = func_get_args();
        $filter = array(
            'created_at_start' => date('Y-m-d', strtotime($date)),
            'created_at_end'   => date('Y-m-d', strtotime($date . '+1 day')),
            'type' => Cp::TYPE_MESSAGE,
        );
        if($brandId){
            $filter += array(
                'brand_id'   => $brandId,
            );
        }
        // メンテDBから取得
        $mainte_db = new aafwDataBuilder('maintedb');
        $cpUsers = $mainte_db->getMessageNumKPI($filter);

        return $cpUsers[0]['numbers'];
    }
}
