<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class CountBrandCreatedSegments implements IManagerKPI {

    function doExecute() {
        $filter = array(
            'status' => Segment::STATUS_ACTIVE,
            'archive_flg' => Segment::ARCHIVE_OFF,
        );
        // メンテDBから取得
        $mainte_db = new aafwDataBuilder('maintedb');
        $BrandsCreatedSegments = $mainte_db->getCountBrandCreatedSegments($filter);


        $brand_filter = array(
            'option_id' => BrandOptions::OPTION_SEGMENT,
        );
        $BrandsHasSegmentOption = $mainte_db->getCountBrandHasSegmentOption($brand_filter);

        $result = $BrandsCreatedSegments[0]['total'] / $BrandsHasSegmentOption[0]['total'] * 100;
        return $result;
    }
}