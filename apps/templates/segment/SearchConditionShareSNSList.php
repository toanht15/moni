<?php
if ($data['target_data']['target_type'] == SegmentCreateSqlService::SEARCH_PHOTO_SHARE_SNS) {
    $key_name = 'search_photo_share_sns/' . $data['target_data']['target_id'] . '/';
} elseif ($data['target_data']['target_type'] == SegmentCreateSqlService::SEARCH_PHOTO_SHARE_SNS) {
    $key_name = 'search_popular_vote_share_sns/' . $data['target_data']['target_id'] . '/';
}
?>
<ul class="status">
    <li>
        <label>
            <input type="checkbox" name="<?php assign($key_name . SocialAccount::SOCIAL_MEDIA_FACEBOOK); ?>" value="<?php assign(SocialAccount::SOCIAL_MEDIA_FACEBOOK); ?>"
                <?php assign($data['condition_data'][$key_name . SocialAccount::SOCIAL_MEDIA_FACEBOOK] === strval(SocialAccount::SOCIAL_MEDIA_FACEBOOK) ? 'checked' : '')?>>
            <span class="iconFB2">Facebook</span>Facebook
        </label>
    </li>
    <li>
        <label>
            <input type="checkbox" name="<?php assign($key_name . SocialAccount::SOCIAL_MEDIA_TWITTER); ?>" value="<?php assign(SocialAccount::SOCIAL_MEDIA_TWITTER); ?>"
                <?php assign($data['condition_data'][$key_name . SocialAccount::SOCIAL_MEDIA_TWITTER] === strval(SocialAccount::SOCIAL_MEDIA_TWITTER) ? 'checked' : '')?>>
            <span class="iconTW2">Twitter</span>Twitter
        </label>
    </li>
    <li>
        <label>
            <input type="checkbox" name="<?php assign($key_name) ?>-1" value="-1"
                <?php assign($data['condition_data'][$key_name . '-1'] === '-1' ? 'checked' : '')?>>
            未シェア
        </label>
    </li>
    <!-- /.status --></ul>