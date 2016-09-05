<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class SegmentProvisionConditionComponent extends aafwWidgetBase {

    public function doService($params = array()) {
        $choices = array();
        $create_sql_service = $this->getService('SegmentCreateSqlService');

        $split_key = explode('/', $params['condition_key']);
        if (SegmentCreateSqlService::isCampaignCondition($split_key[0])) {
            if ($split_key[0] == SegmentCreateSqlService::SEARCH_PARTICIPATE_CONDITION) {
                $condition_key = $params['condition_key'];
            } elseif ($split_key[0] == SegmentCreateSqlService::SEARCH_QUESTIONNAIRE) {
                $condition_key = $split_key[0] . '/' . $split_key[2];
            } else {
                $condition_key = $split_key[0] . '/' . $split_key[1];
            }
        } else {
            $condition_key = $params['condition_key'];
        }

        $condition_data = $create_sql_service->getConditionData($condition_key, $params['condition_value']);

        foreach ($condition_data as $data) {
            $choices[] = $data['content'];
        }
        $params['condition_data'] = array(
            'title' => $data['title'],
            'content' => implode(',', $choices)
        );

        $condition_json_data[$params['condition_key']] = $params['condition_value'];
        $params['condition_data']['json_data'] = json_encode($condition_json_data);

        $segment_creator_service = $this->getService('SegmentCreatorService', array($params['brand_id']));

        list($target_key, $target_id) = $create_sql_service->parseSearchKey($params['condition_key']);

        if ($target_key == SegmentCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE || $target_key == SegmentCreateSqlService::SEARCH_QUESTIONNAIRE) {
            $split_data = explode('/', $params['condition_key']);
            $target_id = $split_data[1] . '/' . $split_data[2];
        }

        $params['condition_view'] = $segment_creator_service->getConditionViewByType($target_key, $target_id, $params['condition_value']);

        return $params;
    }
}
