<?php
$key_name = 'search_import_value/' . $data['target_id'] . '/';
$service_factory = new aafwServiceFactory();
$cp_popular_vote_action_service = $service_factory->create('CpPopularVoteActionService');
$cp_popular_vote_action = $cp_popular_vote_action_service->getCpPopularVoteActionByCpActionId($data['target_id']);
$cp_popular_vote_candidates = $cp_popular_vote_action->getCpPopularVoteCandidates(array('del_flg' => 0));
?>
<ul class="status">
    <?php foreach ($cp_popular_vote_candidates as $cp_popular_vote_candidate): ?>
        <li>
            <label>
                <?php $name = 'search_popular_vote_candidate/' . $data['target_id'] . '/' . $cp_popular_vote_candidate->id ?>
                <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign($cp_popular_vote_candidate->id) ?>"
                    <?php assign($data['condition_data'][$name] === strval($cp_popular_vote_candidate->id) ? 'checked' : '')?>>
                <?php assign(Util::cutTextByWidth($cp_popular_vote_candidate->title, 100)); ?>
            </label>
        </li>
    <?php endforeach; ?>
    <li>
        <label>
            <?php $name = 'search_popular_vote_candidate/' . $data['target_id'] . '/' . CpPopularVoteCandidate::SEARCH_NOT_VOTED ?>
            <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(CpPopularVoteCandidate::SEARCH_NOT_VOTED) ?>"
                <?php assign($data['condition_data'][$name] === strval(CpPopularVoteCandidate::SEARCH_NOT_VOTED) ? 'checked' : '')?>>
            未投票
        </label>
    </li>
    <!-- /.status --></ul>