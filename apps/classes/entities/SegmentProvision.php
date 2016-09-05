<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class SegmentProvision extends aafwEntityBase {

    const DEFAULT_SEGMENT_PROVISION         = 0;
    const UNCONDITIONAL_SEGMENT_PROVISION   = 1;
    const UNCLASSIFIED_SEGMENT_PROVISION    = 2;

    public function getProvisionTextArray() {
        $service_factory = new aafwServiceFactory();
        $create_sql_service = $service_factory->create('SegmentCreateSqlService');
        $provision_text = $create_sql_service->getConditionsData(json_decode($this->provision, true));
        
        return $provision_text;
    }
}