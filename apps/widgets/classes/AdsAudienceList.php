<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class AdsAudienceList extends aafwWidgetBase {

    public function doService($params = array()) {

        /** @var AdsService $ads_service */
        $ads_service = $this->getService('AdsService');
        $params['ads_accounts'] = $ads_service->findValidAdsAccountByBrandUserRelationId($params['brand_user_relation_id']);
        $params['audiences'] = $this->fetchAudienceData($params['audiences']);

        return $params;
    }

    public function fetchAudienceData($audiences) {

        $list_audience_data = array();

        /** @var AdsService $ads_service */
        $ads_service = $this->getService('AdsService');

        foreach($audiences as $audience) {

            $audience_data = $audience;

            $account = $ads_service->findAdsAccountById($audience['account_id']);

            if($account) {
                $audience_data['account_name'] = $account->account_name;
                $audience_data['social_app_id'] = $account->social_app_id;

                //Twitter 場合、EmailやIDオーディエンスを取得
                if($account->isTwitterAccount()) {
                    $relations = $ads_service->findAudiencesAccountsRelationsByAccountIdAndAudienceId($audience['account_id'], $audience['audience_id']);
                    $sns_audience_ids = array();
                    foreach($relations as $relation) {
                        $sns_audience_ids[] = $relation->sns_audience_id;
                    }

                    $audience_data['sns_audience_id'] = $sns_audience_ids;
                }

                //エラーアカウント
                $extra_data = json_decode($account->extra_data, true);

                $audience_data['error_account'] = $extra_data['error'] ? true : false;
            }

            if($audience['relation_id']) {
                $last_taget_log = $ads_service->findLastSendTarget(array($audience['relation_id']));
                if($last_taget_log) {
                    $audience_data['last_send_target_date'] = date('Y/m/d', strtotime($last_taget_log->created_at));
                    $audience_data['last_send_target_status'] = $last_taget_log->status;
                    $audience_data['last_send_target_count'] = $last_taget_log->total;
                }
            }

            $list_audience_data[] = $audience_data;
        }

        return $list_audience_data;
    }

    public function getConditionBrief($audience) {

        /** @var SegmentCreateSqlService $segment_create_sql_service */
        $segment_create_sql_service = $this->getService('SegmentCreateSqlService');

        /** @var SegmentService $segment_service */
        $segment_service = $this->getService('SegmentService');

        /** @var SegmentActionLogService $segment_action_log_service */
        $segment_action_log_service = $this->getService('SegmentActionLogService');

        $condition_brief_text = '--- / ---';

        if($audience['audience_search_type'] == AdsAudience::SEACH_TYPE_ADS) {
            $condition_brief_text = $segment_create_sql_service->getConditionsBriefText(json_decode($audience['audience_search_condition'],true));
        } elseif($audience['audience_search_type'] == AdsAudience::SEACH_TYPE_SEGMENT) {

            $segment_action_log = $segment_action_log_service->findSegmentActionLogById($audience['audience_search_condition']);

            if($segment_action_log) {
                $segment_provisions = $segment_service->findSegmentProvisonsByIds($segment_action_log->segment_provison_ids);
            }

            $provision_name_array = array();

            foreach($segment_provisions as $segment_provision) {

                if($segment_provision->name == '') {
                    $segment = $segment_service->getSegmentById($segment_provision->segment_id);
                    $provision_name_array[] = $segment->name;
                } else {
                    $provision_name_array[] = $segment_provision->name;
                }
            }
            $condition_brief_text = Util::cutTextByWidth(implode('/', $provision_name_array), 500);
        }

        return $condition_brief_text;
    }
}
