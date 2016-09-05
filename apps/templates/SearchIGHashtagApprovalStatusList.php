<li>
    <label>
        <?php $name = 'search_instagram_hashtag_approval_status/' . $data['action_id'] . '/' . InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT) ?>"
            <?php assign($data['search_instagram_hashtag_approval_status'][$name] === strval(InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT) ? 'checked' : '')?>>未承認
    </label>
</li>
<li>
    <label>
        <?php $name = 'search_instagram_hashtag_approval_status/' . $data['action_id'] . '/' . InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE) ?>"
            <?php assign($data['search_instagram_hashtag_approval_status'][$name] === strval(InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE) ? 'checked' : '')?>>承認
    </label>
</li>
<li>
    <label>
        <?php $name = 'search_instagram_hashtag_approval_status/' . $data['action_id'] . '/' . InstagramHashtagUserPost::APPROVAL_STATUS_REJECT ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(InstagramHashtagUserPost::APPROVAL_STATUS_REJECT) ?>"
            <?php assign($data['search_instagram_hashtag_approval_status'][$name] === strval(InstagramHashtagUserPost::APPROVAL_STATUS_REJECT) ? 'checked' : '')?>>非承認
    </label>
</li>