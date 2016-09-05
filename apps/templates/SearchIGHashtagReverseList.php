<li>
    <label>
        <?php $name = 'search_instagram_hashtag_reverse/' . $data['action_id'] . '/' . InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT) ?>"
            <?php assign($data['search_instagram_hashtag_reverse'][$name] === strval(InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT) ? 'checked' : '')?>>登録後投稿
    </label>
</li>
<li>
    <label>
        <?php $name = 'search_instagram_hashtag_reverse/' . $data['action_id'] . '/' . InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID) ?>"
            <?php assign($data['search_instagram_hashtag_reverse'][$name] === strval(InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID) ? 'checked' : '')?>>投稿後登録
    </label>
</li>