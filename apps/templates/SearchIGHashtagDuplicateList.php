<li>
    <label>
        <?php $name = 'search_instagram_hashtag_duplicate/' . $data['action_id'] . '/' . InstagramHashtagUser::SEARCH_EXISTS ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(InstagramHashtagUser::SEARCH_EXISTS) ?>"
            <?php assign($data['search_instagram_hashtag_duplicate'][$name] === strval(InstagramHashtagUser::SEARCH_EXISTS) ? 'checked' : '')?>>あり
    </label>
</li>
<li>
    <label>
        <?php $name = 'search_instagram_hashtag_duplicate/' . $data['action_id'] . '/' . InstagramHashtagUser::SEARCH_NOT_EXISTS ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(InstagramHashtagUser::SEARCH_NOT_EXISTS) ?>"
            <?php assign($data['search_instagram_hashtag_duplicate'][$name] === strval(InstagramHashtagUser::SEARCH_NOT_EXISTS) ? 'checked' : '')?>>なし
    </label>
</li>