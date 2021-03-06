<?php
AAFW::import('jp.aainc.classes.batch.SummaryInteractiveActionBaseCount');
class SummaryTwitterActionReplyCount extends SummaryInteractiveActionBaseCount {
    public function getDataSnsActionCountLogs() {
        $params = array(
            'data_type'             => DetailCrawlerUrl::DATA_TYPE_REPLY,
            'social_media_id'       => SocialAccount::SOCIAL_MEDIA_TWITTER,
            'social_app_id'         => SocialApps::PROVIDER_TWITTER,
        );
        $sns_action_count_data = $this->db->summaryTwitterActionReplyCount($params);
        return $sns_action_count_data;
    }
}