<?php
AAFW::import('jp.aainc.classes.batch.SummaryInteractiveActionBaseCount');
class SummaryFacebookActionLikeCount extends SummaryInteractiveActionBaseCount {
    public function getDataSnsActionCountLogs() {
        $params = array(
            'data_type'             => DetailCrawlerUrl::DATA_TYPE_LIKE,
            'social_media_id'       => SocialAccount::SOCIAL_MEDIA_FACEBOOK,
            'social_app_id'         => SocialApps::PROVIDER_FACEBOOK,
        );
        $sns_action_count_data = $this->db->summaryFacebookActionLikeCount($params);
        return $sns_action_count_data;
    }
}