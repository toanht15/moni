<div class="itemTableWrap">
    <table class="itemTable">
        <thead>
        <tr>
            <th rowspan="2" class="jsAreaToggleWrap">
                評価<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchMemberRate.php', array(
                    'search_rate' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE]
                ))) ?>
            </th>
            <th class="jsAreaToggleWrap" colspan="3">
                <?php assign(Util::cutTextByWidth($data['cp_popular_vote_action']->title, 750)); ?>
            </th>
        </tr>
        <tr>
            <th class="jsAreaToggleWrap">
                投票
                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_CANDIDATE . '/' . $data['display_action_id']] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchPopularVoteCandidate.php', array(
                    'cp_popular_vote_candidates' => $data['cp_popular_vote_action']->getCpPopularVoteCandidates(array('del_flg' => 0)),
                    'search_popular_vote_candidate' => $data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_CANDIDATE . '/' . $data['display_action_id']],
                    'action_id'              => $data['display_action_id']
                ))) ?>
            </th>
            <th class="jsAreaToggleWrap">
                シェアSNS
                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_SNS . '/' . $data['display_action_id']] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchPopularVoteShareSns.php', array(
                    'search_popular_vote_share_sns' => $data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_SNS . '/' . $data['display_action_id']],
                    'action_id'              => $data['display_action_id']
                ))) ?>
            </th>
            <th class="jsAreaToggleWrap">
                シェアされた投票理由
                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_TEXT . '/' . $data['display_action_id']] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchPopularVoteShareText.php', array(
                    'search_popular_vote_share_text' => $data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_TEXT . '/' . $data['display_action_id']],
                    'action_id'               => $data['display_action_id']
                ))) ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data['fan_list_users'] as $fan_list_user): ?>
            <?php if($fan_list_user->mes_id):?>
                <tr class="sendUser">
            <?php elseif($fan_list_user->tar_id): ?>
                <tr class="checkedUser">
            <?php else: ?>
                <tr>
            <?php endif; ?>

            <td class="userRating">
                <?php  $rate_info = $data['brand_user_relation_service']->getMemberRate($fan_list_user->rate);  ?>
                <img src="<?php assign($this->setVersion($rate_info['image_url'])) ?>" width="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" height="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" alt="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 'block user' : 'rating '.$fan_list_user->rate )?>"><?php assign($rate_info['rate']) ?>
                <p class="ratingBox"><a class="ratingBlock" id="<?php assign($fan_list_user->brands_users_relations_id) ?>">ブロック</a><span class="starRating" id="<?php assign($fan_list_user->brands_users_relations_id) ?>" data-score="<?php assign($fan_list_user->rate)?>"></span></p>
            </td>

            <?php $popular_vote_user = $data['popular_vote_user_service']->getPopularVoteUserByIds($data['cp_popular_vote_action']->cp_action_id, $fan_list_user->cp_user_id) ?>
            <?php if($popular_vote_user): ?>
                <td>
                    <?php foreach($popular_vote_user->getCpPopularVoteCandidates(array('del_flg' => 0)) as $cp_popular_vote_candidate): ?>
                        <img src="<?php assign($cp_popular_vote_candidate->thumbnail_url); ?>" class="postPreview" alt="thumbnail">
                        <?php assign(Util::cutTextByWidth($cp_popular_vote_candidate->title, 100)); ?>
                    <?php endforeach; ?>
                </td>
                <td>
                    <span class="accountList">
                        <?php $popular_vote_user_share = null; ?>
                        <?php foreach($popular_vote_user->getPopularVoteUserShares(array('execute_status' => MultiPostSnsQueue::EXECUTE_STATUS_SUCCESS)) as $popular_vote_user_share): ?>
                            <span class="icon<?php assign(SocialAccount::$socialMediaTypeIcon[$popular_vote_user_share->social_media_type])?>2"><?php assign(SocialAccount::$socialMediaTypeName[$popular_vote_user_share->social_media_type]) ?></span>
                        <?php endforeach; ?>
                    </span>
                </td>
                <td>
                    <?php assign($popular_vote_user_share ? Util::cutTextByWidth($popular_vote_user_share->share_text, 300) : ''); ?>
                </td>
            <?php else: ?>
                <td></td>
                <td></td>
                <td></td>
            <?php endif; ?>
            </tr>
        <?php endforeach; ?>

        <?php for($i = 1; $i <= 20 - count($data['fan_list_users']); $i++): ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php endfor; ?>
        </tbody>
        <!-- /.itemTable --></table>
    <!-- /.itemTableWrap --></div>
