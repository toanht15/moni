<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class BrandMaxRelationNoService extends aafwServiceBase {

    private $brand_max_relation_no;

    public function __construct() {
        $this->brand_max_relation_no = $this->getModel('BrandMaxRelationNos');
    }

    public function getMaxNoByBrandIdForUpdate($brand_id) {
        $filter = array(
            'brand_id' => $brand_id,
            'for_update' => true
        );
        return $this->brand_max_relation_no->findOne($filter);
    }

    public function setMaxNo($brand_max_relation_no) {
        $this->brand_max_relation_no->save($brand_max_relation_no);
    }
}
