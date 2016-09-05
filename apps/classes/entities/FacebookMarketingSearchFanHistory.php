<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class FacebookMarketingSearchFanHistory extends aafwEntityBase {

    protected $_Relations = array(
        'FacebookMarketingAudiences' => array(
            'audience_id' => 'id',
        )
    );

    const SEACH_TYPE_ADS = 0;
    const SEACH_TYPE_SEGMENT = 1;
}
