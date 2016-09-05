<li>
    <label>
        <input type="checkbox" name="search_popular_vote_share_sns/<?php assign($data['action_id']) ?>/<?php assign(SocialAccount::SOCIAL_MEDIA_FACEBOOK); ?>" value="<?php assign(SocialAccount::SOCIAL_MEDIA_FACEBOOK); ?>"
            <?php assign($data['search_popular_vote_share_sns']['search_popular_vote_share_sns/' . $data['action_id'] . '/' . SocialAccount::SOCIAL_MEDIA_FACEBOOK] === strval(SocialAccount::SOCIAL_MEDIA_FACEBOOK) ? 'checked' : '')?>>
        <span class="iconFB2">Facebook</span>Facebook
    </label>
</li>
<li>
    <label>
        <input type="checkbox" name="search_popular_vote_share_sns/<?php assign($data['action_id']) ?>/<?php assign(SocialAccount::SOCIAL_MEDIA_TWITTER); ?>" value="<?php assign(SocialAccount::SOCIAL_MEDIA_TWITTER); ?>"
            <?php assign($data['search_popular_vote_share_sns']['search_popular_vote_share_sns/' . $data['action_id'] . '/' . SocialAccount::SOCIAL_MEDIA_TWITTER] === strval(SocialAccount::SOCIAL_MEDIA_TWITTER) ? 'checked' : '')?>>
        <span class="iconTW2">Twitter</span>Twitter
    </label>
</li>
<li>
    <label>
        <input type="checkbox" name="search_popular_vote_share_sns/<?php assign($data['action_id']) ?>/-1" value="-1"
            <?php assign($data['search_popular_vote_share_sns']['search_popular_vote_share_sns/' . $data['action_id'] . '/-1'] === '-1' ? 'checked' : '')?>>
        未シェア
    </label>
</li>