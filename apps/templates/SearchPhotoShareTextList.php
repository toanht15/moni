<li>
    <label>
        <?php $name = 'search_photo_share_text/' . $data['action_id'] . '/' . PhotoUserShare::SEARCH_EXISTS ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(PhotoUserShare::SEARCH_EXISTS) ?>"
            <?php assign($data['search_photo_share_text'][$name] === strval(PhotoUserShare::SEARCH_EXISTS) ? 'checked' : '')?>>あり
    </label>
</li>
<li>
    <label>
        <?php $name = 'search_photo_share_text/' . $data['action_id'] . '/' . PhotoUserShare::SEARCH_NOT_EXISTS ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(PhotoUserShare::SEARCH_NOT_EXISTS) ?>"
            <?php assign($data['search_photo_share_text'][$name] === strval(PhotoUserShare::SEARCH_NOT_EXISTS) ? 'checked' : '')?>>なし
    </label>
</li>