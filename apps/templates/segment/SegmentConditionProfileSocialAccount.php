<?php
    $is_link = 'search_social_account/' . $data['target_id'] . '/' . CpCreateSqlService::LINK_SNS;
    $not_link = 'search_social_account/' . $data['target_id'] . '/' . CpCreateSqlService::NOT_LINK_SNS;
    $disabled = $data['condition_data'][$is_link] ? '' : 'disabled';
?>

<dl class="jsProfileSocialAccountCondition">
    <dd><input type="checkbox" name="<?php assign($is_link) ?>" value="1" class="jsSocialAccountConnect" <?php assign($data['condition_data'][$is_link] ? 'checked' : '')?>>連携済み</dd>
    <dd><input type="checkbox" name="<?php assign($not_link) ?>" value="1" <?php assign($data['condition_data'][$not_link] ? 'checked' : '')?>>未連携</dd>
    <?php if (in_array($data['target_id'], SocialAccountService::$socialHasFriendCount)): ?>
        <dd>
            <ul class="status">友達数　
                <?php write_html($this->formText(
                    'search_friend_count_from/' . $data['target_id'],
                    $data['condition_data']['search_friend_count_from/' . $data['target_id']],
                    array('class'=>'inputNum', $disabled => $disabled)
                )); ?>
                <span class="dash">〜</span>
                <?php write_html($this->formText(
                    'search_friend_count_to/' . $data['target_id'],
                    $data['condition_data']['search_friend_count_to/' . $data['target_id']],
                    array('class'=>'inputNum', $disabled => $disabled)
                )); ?>
        </dd>
    <?php endif ?>
</dl>