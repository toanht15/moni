<?php
$service_factory = new aafwServiceFactory();
/** @var CpUserActionStatusService $cp_user_action_status_service */
$cp_user_action_status_service = $service_factory->create('CpUserActionStatusService');
$cp_action_data = $data["action"]->getCpActionData();
?>
<dl class="stepSetting jsSearchInputBlock">
    <dt class="moduleLabel"><?php assign($data["action_order"].' '.$cp_action_data->title) ?></dt>
    <dd>
        <form>
            <?php $search_key = CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$data['action']->id ?>
            <?php write_html($this->formHidden("search_type", $search_key)) ?>
            <ul class="status">
                <?php write_html($this->parseTemplate("SearchCampaignActionStatusList.php", array("search_conditions" => $data[$search_key], "action" => $data["action"], "search_no" => $data["search_no"]))) ?>
                <!-- /.status --></ul>
        </form>

        <?php if ($data["action"]->type == CpAction::TYPE_QUESTIONNAIRE): ?>
            <?php
            if (!$cp_questionnaire_service) {
                /** @var CpQuestionnaireService $cp_questionnaire_service */
                $cp_questionnaire_service = $service_factory->create('CpQuestionnaireService');
            }
            $questionnaire_action = $cp_questionnaire_service->getCpQuestionnaireAction($data["action"]->id);
            $relations = $cp_questionnaire_service->getRelationsByQuestionnaireActionId($questionnaire_action->id);
            ?>
            <?php foreach($relations as $relation): ?>
                <?php $data['relation_id'] = $relation->id ?>
                <?php write_html($this->parseTemplate("SearchBlockQuestionnaireQuestion.php", $data)) ?>

            <?php endforeach; ?>
        <?php endif; ?>
    </dd>

    <?php if ($data["action"]->type == CpAction::TYPE_PHOTO): ?>
        <?php
        if (!$cp_photo_action_service) {
        /** @var CpPhotoActionService $cp_photo_action_service */
        $cp_photo_action_service = $service_factory->create('CpPhotoActionService');
        }
        $cp_photo_action = $cp_photo_action_service->getCpPhotoAction($data["action"]->id);
        ?>
        <?php if ($cp_photo_action->fb_share_required || $cp_photo_action->tw_share_required): ?>
            <dt class="subStatus">シェアSNS</dt>
            <dd>
                <form>
                    <?php $search_key = CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS . '/' . $data['action']->id; ?>
                    <?php write_html($this->formHidden("search_type", $search_key)) ?>
                    <ul class="status">
                        <?php write_html($this->parseTemplate('SearchPhotoShareSNSList.php', array("search_condition" => $data[$search_key], "action_id" => $data["action"]->id))) ?>
                        <!-- /.status --></ul>
                </form>
            </dd>

            <dt class="subStatus">シェアテキスト</dt>
            <dd>
                <form>
                    <?php $search_key = CpCreateSqlService::SEARCH_PHOTO_SHARE_TEXT . '/' . $data['action']->id; ?>
                    <?php write_html($this->formHidden("search_type", $search_key)) ?>
                    <ul class="status">
                        <?php write_html($this->parseTemplate('SearchPhotoShareTextList.php', array("search_photo_share_text" => $data[$search_key], "action_id" => $data["action"]->id))) ?>
                        <!-- /.status --></ul>
                </form>
            </dd>
        <?php endif; ?>

        <?php if ($cp_photo_action->panel_hidden_flg): ?>
            <dt class="subStatus">検閲</dt>
            <dd>
                <form>
                    <?php $search_key = CpCreateSqlService::SEARCH_PHOTO_APPROVAL_STATUS . '/' . $data['action']->id; ?>
                    <?php write_html($this->formHidden("search_type", $search_key)) ?>
                    <ul class="status">
                        <?php write_html($this->parseTemplate('SearchPhotoApprovalStatusList.php', array("search_photo_approval_status" => $data[$search_key], "action_id" => $data["action"]->id))) ?>
                        <!-- /.status --></ul>
                </form>
            </dd>
        <?php endif; ?>

    <?php elseif ($data["action"]->type === CpAction::TYPE_INSTAGRAM_HASHTAG): ?>
        <?php
        if (!$cp_instagram_hashtag_action_service) {
            /** @var CpInstagramHashtagActionService $cp_instagram_hashtag_action_service */
            $cp_instagram_hashtag_action_service = $service_factory->create('CpInstagramHashtagActionService');
        }
        $cp_instagram_hashtag_action = $cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($data["action"]->id);
        ?>
        <dt class="subStatus">ユーザネーム重複</dt>
        <dd>
            <form>
                <?php $search_key = CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION . '/' . $data['action']->id; ?>
                <?php write_html($this->formHidden("search_type", $search_key)) ?>
                <ul class="status">
                    <?php write_html($this->parseTemplate('SearchIGHashtagDuplicateList.php', array("search_instagram_hashtag_duplicate" => $data[$search_key], "action_id" => $data["action"]->id))) ?>
                    <!-- /.status --></ul>
            </form>
        </dd>

        <dt class="subStatus">登録投稿順序</dt>
        <dd>
            <form>
                <?php $search_key = CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME . '/' . $data['action']->id; ?>
                <?php write_html($this->formHidden("search_type", $search_key)) ?>
                <ul class="status">
                    <?php write_html($this->parseTemplate('SearchIGHashtagReverseList.php', array("search_instagram_hashtag_reverse" => $data[$search_key], "action_id" => $data["action"]->id))) ?>
                    <!-- /.status --></ul>
            </form>
        </dd>

        <?php if ($cp_instagram_hashtag_action->approval_flg): ?>
            <dt class="subStatus">検閲</dt>
            <dd>
                <form>
                    <?php $search_key = CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS . '/' . $data['action']->id; ?>
                    <?php write_html($this->formHidden("search_type", $search_key)) ?>
                    <ul class="status">
                        <?php write_html($this->parseTemplate('SearchIGHashtagApprovalStatusList.php', array("search_instagram_hashtag_approval_status" => $data[$search_key], "action_id" => $data["action"]->id))) ?>
                        <!-- /.status --></ul>
                </form>
            </dd>
        <?php endif; ?>

    <?php elseif($data["action"]->type == CpAction::TYPE_SHARE): ?>
        <dt class="subStatus">シェア状況</dt>
        <dd>
            <form>
                <?php $search_key = CpCreateSqlService::SEARCH_SHARE_TYPE; ?>
                <?php write_html($this->formHidden("search_type", $search_key)) ?>
                <ul class="status">
                    <?php write_html($this->parseTemplate('SearchShareTypeList.php', array("search_share" => $data[$search_key], "search_no" => $data["search_no"]))) ?>
                    <!-- /.status --></ul>
            </form>
        </dd>

        <dt class="subStatus">シェアコメント</dt>
        <dd>
            <form>
                <?php $search_key = CpCreateSqlService::SEARCH_SHARE_TEXT; ?>
                <?php write_html($this->formHidden("search_type", $search_key)) ?>
                <ul class="status">
                    <?php write_html($this->parseTemplate('SearchShareTextList.php', array("search_share" => $data[$search_key], "search_no" => $data["search_no"]))) ?>
                    <!-- /.status --></ul>
            </form>
        </dd>

    <?php elseif ($data["action"]->type == CpAction::TYPE_FACEBOOK_LIKE): ?>
        <dt class="subStatus">Facebookいいね！状況</dt>
        <dd>
            <form>
                <?php $search_key = CpCreateSqlService::SEARCH_FB_LIKE_TYPE. '/' . $data["action"]->id; ?>
                <?php write_html($this->formHidden("search_type", $search_key)) ?>
                <ul class="status">
                    <?php write_html($this->parseTemplate('SearchFbLikeTypeList.php', array("search_fb_like" => $data[$search_key], "search_no" => $data["search_no"],"action_id" => $data["action"]->id))) ?>
                    <!-- /.status --></ul>
            </form>
        </dd>

    <?php elseif ($data["action"]->type == CpAction::TYPE_TWITTER_FOLLOW): ?>
        <dt class="subStatus">Twitterフォロー状況</dt>
        <dd>
            <form>
                <?php $search_key = CpCreateSqlService::SEARCH_TW_FOLLOW_TYPE. '/' . $data["action"]->id; ?>
                <?php write_html($this->formHidden("search_type", $search_key)) ?>
                <ul class="status">
                    <?php write_html($this->parseTemplate('SearchTwFollowTypeList.php', array("search_tw_follow" => $data[$search_key], "search_no" => $data["search_no"],"action_id" => $data["action"]->id))) ?>
                    <!-- /.status --></ul>
            </form>
        </dd>

    <?php elseif ($data["action"]->type == CpAction::TYPE_YOUTUBE_CHANNEL): ?>
        <dt class="subStatus">登録状況</dt>
        <dd>
            <form>
                <?php $search_key = CpCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION. '/' . $data["action"]->id; ?>
                <?php write_html($this->formHidden("search_type", $search_key)) ?>
                <ul class="status">
                    <?php write_html($this->parseTemplate('SearchYtChannelSubscriptionList.php', array("search_ytch_subscription" => $data[$search_key], "search_no" => $data["search_no"],"action_id" => $data["action"]->id))) ?>
                    <!-- /.status --></ul>
            </form>
        </dd>

    <?php elseif ($data["action"]->type == CpAction::TYPE_POPULAR_VOTE): ?>
        <?php
        if (!$cp_popular_vote_action_service) {
            /** @var CpPopularVoteActionService $cp_popular_vote_action_service */
            $cp_popular_vote_action_service = $service_factory->create('CpPopularVoteActionService');
        }
        $cp_popular_vote_action = $cp_popular_vote_action_service->getCpPopularVoteActionByCpActionId($data["action"]->id);
        ?>

        <dt class="subStatus">投票</dt>
        <dd>
            <form>
                <?php $search_key = CpCreateSqlService::SEARCH_POPULAR_VOTE_CANDIDATE. '/' . $data["action"]->id; ?>
                <?php write_html($this->formHidden("search_type", $search_key)) ?>
                <ul class="status">
                    <?php write_html($this->parseTemplate('SearchPopularVoteCandidateList.php', array("search_popular_vote_candidate" => $data[$search_key], "search_no" => $data["search_no"],"action_id" => $data["action"]->id, 'cp_popular_vote_candidates' => $cp_popular_vote_action->getCpPopularVoteCandidates(array('del_flg' => 0))))) ?>
                    <!-- /.status --></ul>
            </form>
        </dd>

        <?php if ($cp_popular_vote_action->fb_share_required || $cp_popular_vote_action->tw_share_required): ?>
            <dt class="subStatus">シェアSNS</dt>
            <dd>
                <form>
                    <?php $search_key = CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_SNS. '/' . $data["action"]->id; ?>
                    <?php write_html($this->formHidden("search_type", $search_key)) ?>
                    <ul class="status">
                        <?php write_html($this->parseTemplate('SearchPopularVoteShareSnsList.php', array("search_popular_vote_share_sns" => $data[$search_key], "search_no" => $data["search_no"],"action_id" => $data["action"]->id))) ?>
                        <!-- /.status --></ul>
                </form>
            </dd>

            <dt class="subStatus">シェアされた投票理由</dt>
            <dd>
                <form>
                    <?php $search_key = CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_TEXT. '/' . $data["action"]->id; ?>
                    <?php write_html($this->formHidden("search_type", $search_key)) ?>
                    <ul class="status">
                        <?php write_html($this->parseTemplate('SearchPopularVoteShareTextList.php', array("search_popular_vote_share_text" => $data[$search_key], "search_no" => $data["search_no"],"action_id" => $data["action"]->id))) ?>
                        <!-- /.status --></ul>
                </form>
            </dd>
        <?php endif; ?>

    <?php endif; ?>
</dl>
