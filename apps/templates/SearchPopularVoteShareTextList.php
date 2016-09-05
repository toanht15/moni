<li>
    <label>
        <?php $name = 'search_popular_vote_share_text/' . $data['action_id'] . '/' . PopularVoteUserShare::SEARCH_EXISTS ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(PopularVoteUserShare::SEARCH_EXISTS) ?>"
            <?php assign($data['search_popular_vote_share_text'][$name] === strval(PopularVoteUserShare::SEARCH_EXISTS) ? 'checked' : '')?>>あり
    </label>
</li>
<li>
    <label>
        <?php $name = 'search_popular_vote_share_text/' . $data['action_id'] . '/' . PopularVoteUserShare::SEARCH_NOT_EXISTS ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(PopularVoteUserShare::SEARCH_NOT_EXISTS) ?>"
            <?php assign($data['search_popular_vote_share_text'][$name] === strval(PopularVoteUserShare::SEARCH_NOT_EXISTS) ? 'checked' : '')?>>なし
    </label>
</li>