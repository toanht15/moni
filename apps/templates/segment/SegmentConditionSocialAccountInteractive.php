<?php
$split_data = explode('/', $data['target_id']);
$app_id = $split_data[0];
$social_media_account_id = $split_data[1];
?>
<ul class="status">
    <li><label><input type="checkbox" name='search_social_account_interactive/<?php assign($app_id . '/' . $social_media_account_id . '/Y')?>' <?php assign($data['condition_data']['search_social_account_interactive/'.$app_id.'/'. $social_media_account_id.'/Y'] ? 'checked' : '')?>><?php assign(SocialApps::$social_media_page_fan_status[$app_id]) ?></label></li>
    <li><label><input type="checkbox" name='search_social_account_interactive/<?php assign($app_id . '/' . $social_media_account_id . '/N')?>' <?php assign($data['search_conditions']['search_social_account_interactive/'.$app_id.'/'. $social_media_account_id.'/N'] ? 'checked' : '')?>><?php assign(SocialApps::$social_media_page_not_fan_status[$app_id]) ?></label></li>
    <!-- /.status --></ul>