<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.entities.BrandsUsersRelation');

class BrandsUsersNum implements IManagerKPI {

    function doExecute() {
        list($date, $brandId) = func_get_args();
        $created_at = date('Y-m-d', strtotime($date .' +1 day'));

        if (!$brandId) {
            $BrandsUsersRelation = aafwEntityStoreFactory::create('BrandsUsersRelations');
            $filter = array(
                'created_at:<' => $created_at,
                'withdraw_flg' => '0'
            );
            return $BrandsUsersRelation->count($filter, 'user_id');
        }

        $defaultSQL =
            "SELECT COUNT(*) FROM
                (SELECT del_flg, withdraw_flg, created_at
                    FROM brands_users_relations WHERE brand_id = {$brandId}) r
                WHERE r.del_flg = '0' AND r.created_at < '{$created_at}' AND r.withdraw_flg = '0'";
        $data_builder = aafwDataBuilder::newBuilder();
        $rs = $data_builder->executeUpdate($defaultSQL);
        $row = $data_builder->fetchResultSet($rs);

        return $row['COUNT(*)'];
    }
}