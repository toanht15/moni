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
                    <th class="jsAreaToggleWrap" colspan="<?php assign($data['colspan']) ?>">
                        <?php assign(Util::cutTextByWidth($data['cp_instagram_hashtag_action']->title, 750)); ?>
                    </th>
                </tr>
                <tr>
                    <th class="jsAreaToggleWrap">写真</th>
                    <th class="jsAreaToggleWrap">コメント</th>
                    <?php if (empty($data['is_hide_personal_info'])): ?>
                        <th class="jsAreaToggleWrap">ユーザネーム</th>
                    <?php endif; ?>
                    <th class="jsAreaToggleWrap snsAccount">
                        ユーザネーム重複
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION . '/' . $data['display_action_id']] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchInstagramHashtagDuplicate.php', array(
                            'search_instagram_hashtag_duplicate' => $data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION . '/' . $data['display_action_id']],
                            'action_id'                          => $data['display_action_id']
                        ))) ?>
                    </th>
                    <th class="jsAreaToggleWrap snsAccount">
                        登録投稿順序
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME . '/' . $data['display_action_id']] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchInstagramHashtagReverse.php', array(
                            'search_instagram_hashtag_reverse' => $data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME . '/' . $data['display_action_id']],
                            'action_id'                        => $data['display_action_id']
                        ))) ?>
                    </th>
                    <?php if($data['cp_instagram_hashtag_action']->approval_flg): ?>
                        <th class="jsAreaToggleWrap snsAccount">
                            検閲
                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS . '/' . $data['display_action_id']] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchInstagramHashtagApprovalStatus.php', array(
                                'search_instagram_hashtag_approval_status' => $data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS . '/' . $data['display_action_id']],
                                'action_id'                                => $data['display_action_id']
                            ))) ?>
                        </th>
                    <?php endif; ?>
                    <th class="jsAreaToggleWrap">登録日時</th>
                    <th class="jsAreaToggleWrap">投稿日時</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['fan_list_users'] as $fan_list_user): ?>
                    <?php if($data['action']->id): ?>
                        <?php $page_user_message = $data['page_user_message'][$fan_list_user->user_id]; ?>
                        <?php if($page_user_message[0]): ?>
                            <tr class="sendUser">
                        <?php elseif($page_user_message[1]): ?>
                            <tr class="checkedUser">
                        <?php else: ?>
                            <tr>
                        <?php endif; ?>
                    <?php else: ?>
                        <tr>
                    <?php endif; ?>

                    <td class="userRating">
                        <?php  $rate_info = $data['brand_user_relation_service']->getMemberRate($fan_list_user->rate);  ?>
                        <img src="<?php assign($this->setVersion($rate_info['image_url'])) ?>" width="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" height="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" alt="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 'block user' : 'rating '.$fan_list_user->rate )?>"><?php assign($rate_info['rate']) ?>
                        <p class="ratingBox"><a class="ratingBlock" id="<?php assign($fan_list_user->brands_users_relations_id) ?>">ブロック</a><span class="starRating" id="<?php assign($fan_list_user->brands_users_relations_id) ?>" data-score="<?php assign($fan_list_user->rate)?>"></span></p>
                    </td>

                    <?php if($hashtag_user = $data['user_hashtag'][$fan_list_user->cp_user_id]): ?>
                        <td>
                            <?php for($i = 0 ; $i < count($hashtag_user['thumbnail']) && $i < 6 ; $i++): ?>
                                <?php if ($hashtag_user['approval_status_status'][$i] != InstagramHashtagUserPost::APPROVAL_STATUS_PRIVATE): ?>
                                    <a href="#instagram_hashtag_edit_modal" class="jsOpenInstagramHashtagModal" data-instagram_hashtag_user_post_id="<?php assign($hashtag_user['id'][$i]) ?>" data-page_type="show_user_list">
                                        <img src="<?php assign($hashtag_user['thumbnail'][$i]) ?>" alt="" class="postPreview"/>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <?php if($i == 6) { assign('...');} ?>
                        </td>
                        <?php $post_text = implode(',', $hashtag_user['post_text']); ?>
                        <td title="<?php assign($post_text) ?>">
                            <?php assign(Util::cutTextByWidth($post_text, 190)) ?>
                        </td>

                        <?php if (empty($data['is_hide_personal_info'])): ?>
                            <td title="<?php assign($hashtag_user['user_name']) ?>">
                                <?php assign(Util::cutTextByWidth($hashtag_user['user_name'], 190)) ?>
                            </td>
                        <?php endif; ?>

                        <td title="<?php assign($hashtag_user['duplicate_flg'] ? 'あり' : 'なし') ?>"><?php assign($hashtag_user['duplicate_flg'] ? 'あり' : 'なし') ?></td>

                        <?php $reverse_post_time = implode(',', $hashtag_user['reverse_post_time']); ?>
                        <td title="<?php assign($reverse_post_time) ?>">
                            <?php assign(Util::cutTextByWidth($reverse_post_time, 190)) ?>
                        </td>

                        <?php if ($data['cp_instagram_hashtag_action']->approval_flg): ?>
                            <?php $approval_status = implode(',', $hashtag_user['approval_status']); ?>
                            <td title="<?php assign($approval_status) ?>">
                                <?php assign(Util::cutTextByWidth($approval_status, 190)) ?>
                            </td>
                        <?php endif; ?>

                        <td title="<?php assign(date('Y/m/d H:i', strtotime($hashtag_user['created_at']))) ?>">
                            <?php assign(date('Y/m/d H:i', strtotime($hashtag_user['created_at']))) ?>
                        </td>

                        <?php $post_date_time = implode(',', $hashtag_user['post_date_time']); ?>
                        <td title="<?php assign($post_date_time) ?>">
                            <?php assign(Util::cutTextByWidth($post_date_time, 190)) ?>
                        </td>

                    <?php else: ?>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <?php if($data['cp_instagram_hashtag_action']->approval_flg): ?>
                            <td></td>
                        <?php endif; ?>
                        <td></td>
                        <td></td>
                    <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                <?php for($i = 1; $i <= CpCreateSqlService::DISPLAY_20_ITEMS-count($data['fan_list_users']); $i++): ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <?php if($data['cp_instagram_hashtag_action']->approval_flg): ?>
                            <td></td>
                        <?php endif; ?>
                        <td></td>
                        <?php if (empty($data['is_hide_personal_info'])): ?>
                            <td></td>
                        <?php endif ?>
                    </tr>
                <?php endfor; ?>
            </tbody>
        <!-- /.itemTable --></table>
    <!-- /.itemTableWrap --></div>
    <?php write_html($this->formHidden('instagram_hashtag_edit_modal_url', Util::rewriteUrl('admin-cp', 'api_get_instagram_hashtag_edit_modal.json'))) ?>
