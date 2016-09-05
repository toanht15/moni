<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.SocialAccountService');

class SearchConditionViewCol extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var CpCreateSqlService $cp_create_sql_service */
        $create_sql_service = $this->getService('SegmentCreateSqlService');

        $params['conditions'] = $create_sql_service->getConditionsData($params['search_conditions']);

        return $params;
    }
}
