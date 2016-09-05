<?php foreach ($data['cp_popular_vote_candidates'] as $cp_popular_vote_candidate): ?>
    <li>
        <label>
            <?php $name = 'search_popular_vote_candidate/' . $data['action_id'] . '/' . $cp_popular_vote_candidate->id ?>
            <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign($cp_popular_vote_candidate->id) ?>"
                <?php assign($data['search_popular_vote_candidate'][$name] === strval($cp_popular_vote_candidate->id) ? 'checked' : '')?>>
            <?php assign(Util::cutTextByWidth($cp_popular_vote_candidate->title, 100)); ?>
        </label>
    </li>
<?php endforeach; ?>
<li>
    <label>
        <?php $name = 'search_popular_vote_candidate/' . $data['action_id'] . '/' . CpPopularVoteCandidate::SEARCH_NOT_VOTED ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(CpPopularVoteCandidate::SEARCH_NOT_VOTED) ?>"
            <?php assign($data['search_popular_vote_candidate'][$name] === strval(CpPopularVoteCandidate::SEARCH_NOT_VOTED) ? 'checked' : '')?>>
        未投票
    </label>
</li>