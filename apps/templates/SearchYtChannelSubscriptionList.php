<?php
$search_ytch_subscription_type = [
    CpYoutubeChannelUserLog::STATUS_FOLLOWED  => CpYoutubeChannelUserLog::$youtube_status_string[CpYoutubeChannelUserLog::STATUS_FOLLOWED],
    CpYoutubeChannelUserLog::STATUS_FOLLOWING => CpYoutubeChannelUserLog::$youtube_status_string[CpYoutubeChannelUserLog::STATUS_FOLLOWING],
    CpYoutubeChannelUserLog::STATUS_SKIP      => CpYoutubeChannelUserLog::$youtube_status_string[CpYoutubeChannelUserLog::STATUS_SKIP]
]
?>
<?php foreach ($search_ytch_subscription_type as $key => $value): ?>
    <li>
        <?php write_html($this->formCheckbox(
            'search_ytch_subscription_type/' . $data['action_id'] . '/' . $key . '/' . CpCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION . '/' . $data["search_no"],
            $data['search_ytch_subscription']['search_ytch_subscription_type/' . $data['action_id'] . '/' . $key],
            array('checked' => $data['search_ytch_subscription']['search_ytch_subscription_type/' . $data['action_id'] . '/' . $key] ? "checked" : ""),
            array('1' => $value)
        ))?>
    </li>
<?php endforeach; ?>