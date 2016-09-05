    <section class="userListWrap">

        <?php if($data['show_segment_condition']): ?>
            <?php write_html(aafwWidgets::getInstance()->loadWidget('SegmentMessageActionConditionList')->render(array(
                'brand'   => $data['brand'],
                'segment_condition_session' => $data['segment_condition_session']
            ))) ?>
        <?php endif; ?>

        <div class="pager1">
            <p><strong>件数計算中...</strong></p>
        </div>

        <?php write_html($this->parseTemplate('CpUserCandidateNumber.php', array(
            'exist_target' => $data['page_reservation_target'] ? true : false,
            'is_include_type_announce' => $data['is_include_type_announce'],
            'is_include_type_announce_delivery' => $data['is_include_type_announce_delivery'],
            'fixed_target' => $data['fixed_target'],
            'manager_permission' => $data['manager_full_control'] ? true : false,
            'delivered_message' => $data['delivered_message'],
            'has_fix_target_step' => $data['has_fix_target_step'] ? true : false,
            'selected_target' => $data['selected_target'] ? true : false,
        ))) ?>

        <div class="userListMessage" style="display:none"><p>件数計算中...</p></div>
        <?php write_html($this->formHidden('all_not_sent_user_count', $data['all_not_sent_user_count']))?>
        <form id="frmSearchFan1" name="frmSearchFan1">
            <?php write_html($this->formHidden('page_info', $data['list_page']['page_no'].'/'.$data['list_page']['display_action_id'].'/'.$data['list_page']['tab_no']))?>
            <?php $this->search_no = 1; ?>
            <?php write_html($this->parseTemplate('CpUserSearch.php', array(
                'brand_id'           => $data['list_page']['brand_id'],
                'cp_id'              => $data['list_page']['cp_id'],
                'search_condition'   => $data['search_condition'],
                'hasSearchCondition' => $data['hasSearchCondition'],
                'show_sent_time'     => $data['show_sent_time'],
                'cp_actions'         => $data['cp_actions'],
                'action'          => $data['cp_action'],
                'duplicateAddressShowType' => CpCreateSqlService::SHIPPING_ADDRESS_USER_DUPLICATE,
                'isShowDuplicateAddressCpUserList' => $data['isShowDuplicateAddressCpUserList']
            ))) ?>
            <?php write_html($this->csrf_tag()); ?>
        </form>
        <div class="userListCont">
            <ul class="userName">
                <form id="formAddTarget" name="formAddTarget" action="<?php assign(Util::rewriteUrl('admin-cp', 'update_fan_target.json')); ?>" method="POST">
                    <li class="allUser">
                        <label><input type="checkbox" class="jsCountAll" name="user[]" value="" <?php assign($data['cp']->status == Cp::STATUS_SCHEDULE && $data['cp']->send_mail_flg == Cp::FLAG_SHOW_VALUE ? 'disabled' : '')?>></label>
                        <a href="javascript:void(0)" class="<?php assign($data['hasSearchCondition'] ? 'iconBtnSort' : 'btnArrowB1 iconHelp')?> jsSortItem" data-query="some_query">
                            <span class="text">ヘルプ</span>
                              <span class="textBalloon1">
                                <span>
                                  参加履歴などで対象を絞り込めます
                                </span>
                            <!-- /.textBalloon1 --></span>
                            絞り込む</a>
                    </li>

                    <?php foreach($data['fan_list_users'] as $fan_list_user): ?>
                        <?php $user_info = $fan_list_user->getBrandcoUser(); ?>
                        <?php $page_user_message = $data['page_user_message'][$fan_list_user->user_id]; ?>
                        <?php if($page_user_message[0]): ?>
                            <li class="sendUser">
                        <?php elseif($page_user_message[2]): ?>
                            <li class="fixedUser checkedUser">
                        <?php elseif($page_user_message[1]): ?>
                            <li class="checkedUser">
                        <?php else: ?>
                            <li>
                        <?php endif; ?>

                        <?php if($page_user_message[0]): ?>
                            <label title="<?php assign(!empty($data['is_hide_personal_info']) ? '' : $user_info->name) ?>">
                                <input type="checkbox" class="jsCountTarget" name="user[]" value="<?php assign($fan_list_user->user_id) ?>" checked disabled>
                                <img src="<?php assign($user_info->profile_image_url ? $user_info->profile_image_url : config('Static.Url').'/img/base/imgUser1.jpg') ?>" width="20" height="20" alt="<?php assign(!empty($data['is_hide_personal_info']) ? '' : $user_info->name) ?>" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';">
                                <?php assign(!empty($data['is_hide_personal_info']) ? '' : Util::cutTextByWidth($user_info->name, 86)) ?>
                            </label>
                        <?php else: ?>
                            <label title="<?php assign(!empty($data['is_hide_personal_info']) ? '' : $user_info->name) ?>">
                                <input type="checkbox" class="jsCountTarget" name="user[]" value="<?php assign($fan_list_user->user_id) ?>" <?php assign($data['cp']->status == Cp::STATUS_SCHEDULE && $data['cp']->send_mail_flg == Cp::FLAG_SHOW_VALUE ? 'disabled' : '')?>>
                                <img src="<?php assign($user_info->profile_image_url ? $user_info->profile_image_url : config('Static.Url').'/img/base/imgUser1.jpg') ?>" width="20" height="20" alt="<?php assign(!empty($data['is_hide_personal_info']) ? '' : $user_info->name) ?>" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';">
                                <?php assign(!empty($data['is_hide_personal_info']) ? '' : Util::cutTextByWidth($user_info->name, 86)) ?>
                            </label>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                    <?php write_html($this->csrf_tag()); ?>
                    <?php for($i = 1; $i <= CpCreateSqlService::DISPLAY_20_ITEMS-count($data['fan_list_users']); $i++): ?>
                        <li></li>
                    <?php endfor; ?>
                    <?php write_html($this->formHidden('checkedUser', ''))?>
                    <?php write_html($this->formHidden('action_id', $data['list_page']['action_id'])) ?>
                    <?php write_html($this->formHidden('cp_id', $data['list_page']['cp_id'])) ?>
                    <?php write_html($this->formHidden('fan_count', count($data['fan_list_users']))) ?>
                    <?php write_html($this->formHidden('page_sent_user_count', $data['page_sent_user_count'])) ?>
                </form>
            </ul>

            <form id="frmSearchFan2" name="frmSearchFan2">
                <?php write_html($this->formHidden('page_info', $data['list_page']['page_no'].'/'.$data['list_page']['display_action_id'].'/'.$data['list_page']['tab_no']))?>
                <?php $this->search_no = 2; ?>
                <?php if($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_PROFILE): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpUserProfile')->render(array(
                        'brand'              => $data['brand'],
                        'fan_list_users'     => $data['fan_list_users'],
                        'search_condition'   => $data['search_condition'],
                        'fan_limit'          => $data['list_page']['limit'],
                        'action'             => $data['cp_action'],
                        'page_user_message'  => $data['page_user_message'],
                        'search_no'          => $this->search_no,
                        'isSocialLikesEmpty' => $data['isSocialLikesEmpty'],
                        'cp_id'              => $data['list_page']['cp_id'],
                        'isTwitterFollowsEmpty' => $data['isTwitterFollowsEmpty'],
                        'duplicateAddressShowType' => CpCreateSqlService::SHIPPING_ADDRESS_USER_DUPLICATE,
                        'isShowDuplicateAddressCpUserList' => $data['isShowDuplicateAddressCpUserList'],
                        'list_page'          => $data['list_page'],
                        'is_hide_personal_info' => $data['is_hide_personal_info']
                    ))) ?>
                <?php elseif($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_PARTICIPATE_CONDITION): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpUserEntryCondition')->render(array(
                        'fan_list_users'   => $data['fan_list_users'],
                        'cp_id'            => $data['list_page']['cp_id'],
                        'search_condition' => $data['search_condition'],
                        'show_sent_time'   => $data['show_sent_time'],
                        'fan_limit'        => $data['list_page']['limit'],
                        'action'           => $data['cp_action'],
                        'show_sent_time'   => $data['show_sent_time'],
                        'page_user_message'=> $data['page_user_message'],
                        'search_no'        => $this->search_no
                    ))) ?>
                <?php elseif($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_QUESTIONNAIRE_ANSWER): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpUserQuestionnaireAnswer')->render(array(
                        'brand'            => $data['brand'],
                        'fan_list_users'   => $data['fan_list_users'],
                        'display_action_id'=> $data['list_page']['display_action_id'],
                        'search_condition' => $data['search_condition'],
                        'action'           => $data['cp_action'],
                        'page_user_message'=> $data['page_user_message'],
                        'search_no'        => $this->search_no
                    ))) ?>
                <?php elseif($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_PHOTO): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpUserPhoto')->render(array(
                        'fan_list_users'   => $data['fan_list_users'],
                        'display_action_id'=> $data['list_page']['display_action_id'],
                        'search_condition' => $data['search_condition'],
                        'fan_limit'        => $data['list_page']['limit'],
                        'action'           => $data['cp_action'],
                        'search_no'        => $this->search_no,
                        'page_user_message'=> $data['page_user_message'],
                    ))) ?>
                <?php elseif($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_SHARE): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpUserShare')->render(array(
                        'fan_list_users'   => $data['fan_list_users'],
                        'display_action_id'=> $data['list_page']['display_action_id'],
                        'search_condition' => $data['search_condition'],
                        'action'           => $data['cp_action'],
                        'search_no'        => $this->search_no,
                        'page_user_message'=> $data['page_user_message'],
                    ))) ?>
                <?php elseif($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_INSTAGRAM_HASHTAG): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpUserInstagramHashtag')->render(array(
                        'fan_list_users'   => $data['fan_list_users'],
                        'display_action_id'=> $data['list_page']['display_action_id'],
                        'reservation'      => $data['current_reservation'],
                        'search_condition' => $data['search_condition'],
                        'search_no'        => $this->search_no,
                        'action'           => $data['cp_action'],
                        'page_user_message'=> $data['page_user_message'],
                        'is_hide_personal_info' => $data['is_hide_personal_info']
                    ))) ?>
                <?php elseif($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_YOUTUBE_CHANNEL): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpUserYoutubeChannel')->render(array(
                        'fan_list_users'   => $data['fan_list_users'],
                        'display_action_id'=> $data['list_page']['display_action_id'],
                        'reservation'      => $data['current_reservation'],
                        'search_condition' => $data['search_condition'],
                        'search_no'        => $this->search_no,
                        'action'           => $data['cp_action'],
                        'page_user_message'=> $data['page_user_message'],
                    ))) ?>
                <?php elseif ($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_FACEBOOK_LIKE): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpUserFacebookLike')->render(array(
                        'fan_list_users'    => $data['fan_list_users'],
                        'display_action_id' => $data['list_page']['display_action_id'],
                        'reservation'       => $data['current_reservation'],
                        'search_condition'  => $data['search_condition'],
                        'search_no'         => $this->search_no,
                        'action'            => $data['cp_action'],
                        'page_user_message' => $data['page_user_message'],
                    ))) ?>
                <?php elseif ($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_TWITTER_FOLLOW): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpUserTwitterFollow')->render(array(
                        'fan_list_users'    => $data['fan_list_users'],
                        'display_action_id' => $data['list_page']['display_action_id'],
                        'reservation'       => $data['current_reservation'],
                        'search_condition'  => $data['search_condition'],
                        'search_no'         => $this->search_no,
                        'action'            => $data['cp_action'],
                        'page_user_message' => $data['page_user_message'],
                    ))) ?>
                <?php elseif($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_POPULAR_VOTE): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpUserPopularVote')->render(array(
                        'fan_list_users'   => $data['fan_list_users'],
                        'display_action_id'=> $data['list_page']['display_action_id'],
                        'search_condition' => $data['search_condition'],
                        'action'           => $data['cp_action'],
                        'search_no'        => $this->search_no
                    ))) ?>
                <?php elseif($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_TWEET):?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpUserTweet')->render(array(
                        'fan_list_users'    => $data['fan_list_users'],
                        'display_action_id' => $data['list_page']['display_action_id'],
                        'reservation'       => $data['current_reservation'],
                        'search_condition'  => $data['search_condition'],
                        'search_no'         => $this->search_no,
                        'action'            => $data['cp_action'],
                        'page_user_message' => $data['page_user_message'],
                    )))?>
                <?php endif ?>
                <?php write_html($this->csrf_tag()); ?>

            </form>
        <!-- /.userListCont --></div>

        <ul class="tablink2">
            <?php if($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_PROFILE): ?>
                <li class="current"><span>プロフィール</span></li>
            <?php else: ?>
                <li><a href="javascript:void(0)" data-tab="linkTab" data-tab_no="<?php assign(CpCreateSqlService::TAB_PAGE_PROFILE) ?>" data-page_no="<?php assign($data['list_page']['page_no']) ?>" data-display_action_id="" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_get_search_fan', array($data['list_page']['cp_id'], $data['list_page']['action_id']), array())) ?>">プロフィール</a></li>
            <?php endif; ?>

            <?php if($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_PARTICIPATE_CONDITION): ?>
                <li class="current"><span>参加状況</span></li>
            <?php else: ?>
                <li><a href="javascript:void(0)" data-tab="linkTab" data-tab_no="<?php assign(CpCreateSqlService::TAB_PAGE_PARTICIPATE_CONDITION) ?>" data-page_no="<?php assign($data['list_page']['page_no']) ?>" data-display_action_id="" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_get_search_fan', array($data['list_page']['cp_id'], $data['list_page']['action_id']), array())) ?>">参加状況</a></li>
            <?php endif; ?>
            <?php $action_no = 1;?>
            <?php foreach($data['cp_actions'] as $action): ?>
                <?php if($action->type == CpAction::TYPE_QUESTIONNAIRE): ?>
                    <?php if($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_QUESTIONNAIRE_ANSWER && $data['list_page']['display_action_id'] == $action->id): ?>
                        <li class="current"><span><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．アンケート</span></li>
                    <?php else: ?>
                        <li><a href="javascript:void(0)" data-tab="linkTab" data-tab_no="<?php assign(CpCreateSqlService::TAB_PAGE_QUESTIONNAIRE_ANSWER) ?>" data-page_no="<?php assign($data['list_page']['page_no']) ?>" data-display_action_id="<?php assign($action->id) ?>" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_get_search_fan', array($data['list_page']['cp_id'], $data['list_page']['action_id']), array())) ?>"><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．アンケート</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if($action->type == CpAction::TYPE_PHOTO): ?>
                    <?php if($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_PHOTO && $data['list_page']['display_action_id'] == $action->id): ?>
                        <li class="current"><span><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．写真投稿</span></li>
                    <?php else: ?>
                        <li><a href="javascript:void(0)" data-tab="linkTab" data-tab_no="<?php assign(CpCreateSqlService::TAB_PAGE_PHOTO) ?>" data-page_no="<?php assign($data['list_page']['page_no']) ?>" data-display_action_id="<?php assign($action->id) ?>" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_get_search_fan', array($data['list_page']['cp_id'], $data['list_page']['action_id']), array())) ?>"><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．写真投稿</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if($action->type == CpAction::TYPE_SHARE): ?>
                    <?php if($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_SHARE && $data['list_page']['display_action_id'] == $action->id): ?>
                        <li class="current"><span><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．シェア</span></li>
                    <?php else: ?>
                        <li><a href="javascript:void(0)" data-tab="linkTab" data-tab_no="<?php assign(CpCreateSqlService::TAB_PAGE_SHARE) ?>" data-page_no="<?php assign($data['list_page']['page_no']) ?>" data-display_action_id="<?php assign($action->id) ?>" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_get_search_fan', array($data['list_page']['cp_id'], $data['list_page']['action_id']), array())) ?>"><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．シェア</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if($action->type == CpAction::TYPE_INSTAGRAM_HASHTAG): ?>
                    <?php if($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_INSTAGRAM_HASHTAG && $data['list_page']['display_action_id'] == $action->id): ?>
                        <li class="current"><span><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．Instagram投稿</span></li>
                    <?php else: ?>
                        <li><a href="javascript:void(0)" data-tab="linkTab" data-tab_no="<?php assign(CpCreateSqlService::TAB_PAGE_INSTAGRAM_HASHTAG) ?>" data-page_no="<?php assign($data['list_page']['page_no']) ?>" data-display_action_id="<?php assign($action->id) ?>" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_get_search_fan', array($data['list_page']['cp_id'], $data['list_page']['action_id']), array())) ?>"><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．Instagram投稿</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($action->type == CpAction::TYPE_FACEBOOK_LIKE): ?>
                    <?php if ($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_FACEBOOK_LIKE && $data['list_page']['display_action_id'] == $action->id): ?>
                        <li class="current"><span><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．Facebookいいね！</span></li>
                    <?php else: ?>
                        <li><a href="javascript:void(0)" data-tab="linkTab" data-tab_no="<?php assign(CpCreateSqlService::TAB_PAGE_FACEBOOK_LIKE) ?>" data-page_no="<?php assign($data['list_page']['page_no']) ?>" data-display_action_id="<?php assign($action->id) ?>" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_get_search_fan', array($data['list_page']['cp_id'], $data['list_page']['action_id']), array())) ?>"><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．Facebookいいね！</a></li>
                    <?php endif ?>
                <?php endif ?>
                <?php if ($action->type == CpAction::TYPE_TWITTER_FOLLOW): ?>
                    <?php if ($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_TWITTER_FOLLOW && $data['list_page']['display_action_id'] == $action->id): ?>
                        <li class="current"><span><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．Twitterフォロー</span></li>
                    <?php else: ?>
                        <li><a href="javascript:void(0)" data-tab="linkTab" data-tab_no="<?php assign(CpCreateSqlService::TAB_PAGE_TWITTER_FOLLOW) ?>" data-page_no="<?php assign($data['list_page']['page_no']) ?>" data-display_action_id="<?php assign($action->id) ?>" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_get_search_fan', array($data['list_page']['cp_id'], $data['list_page']['action_id']), array())) ?>"><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．Twitterフォロー</a></li>
                    <?php endif; ?>
                <?php endif;?>
                <?php if ($action->type == CpAction::TYPE_TWEET): ?>
                    <?php if ($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_TWEET && $data['list_page']['display_action_id'] == $action->id): ?>
                        <li class="current"><span><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．ツイート</span></li>
                    <?php else: ?>
                        <li><a href="javascript:void(0)" data-tab="linkTab" data-tab_no="<?php assign(CpCreateSqlService::TAB_PAGE_TWEET) ?>" data-page_no="<?php assign($data['list_page']['page_no']) ?>" data-display_action_id="<?php assign($action->id) ?>" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_get_search_fan', array($data['list_page']['cp_id'], $data['list_page']['action_id']), array())) ?>"><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．ツイート</a></li>
                    <?php endif;?>
                <?php endif;?>
                <?php if($action->type == CpAction::TYPE_YOUTUBE_CHANNEL): ?>
                    <?php if($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_YOUTUBE_CHANNEL && $data['list_page']['display_action_id'] == $action->id): ?>
                        <li class="current"><span><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．YouTubeチャンネル登録</span></li>
                    <?php else: ?>
                        <li><a href="javascript:void(0)" data-tab="linkTab" data-tab_no="<?php assign(CpCreateSqlService::TAB_PAGE_YOUTUBE_CHANNEL) ?>" data-page_no="<?php assign($data['list_page']['page_no']) ?>" data-display_action_id="<?php assign($action->id) ?>" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_get_search_fan', array($data['list_page']['cp_id'], $data['list_page']['action_id']), array())) ?>"><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．YouTubeチャンネル登録</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if($action->type == CpAction::TYPE_POPULAR_VOTE): ?>
                    <?php if($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_POPULAR_VOTE && $data['list_page']['display_action_id'] == $action->id): ?>
                        <li class="current"><span><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．人気投票</span></li>
                    <?php else: ?>
                        <li><a href="javascript:void(0)" data-tab="linkTab" data-tab_no="<?php assign(CpCreateSqlService::TAB_PAGE_POPULAR_VOTE) ?>" data-page_no="<?php assign($data['list_page']['page_no']) ?>" data-display_action_id="<?php assign($action->id) ?>" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_get_search_fan', array($data['list_page']['cp_id'], $data['list_page']['action_id']), array())) ?>"><?php assign(mb_convert_kana($action_no, 'N', 'UTF-8')) ?>．人気投票</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php $action_no += 1;?>
            <?php endforeach; ?>
        </ul>

        <?php write_html($this->parseTemplate('CpUserCandidateNumber.php', array(
            'exist_target' => $data['page_reservation_target'] ? true : false,
            'is_include_type_announce' => $data['is_include_type_announce'],
            'fixed_target' => $data['fixed_target'],
            'manager_permission' => $data['manager_full_control'] ? true : false,
            'delivered_target_message' => $data['delivered_target_message'],
            'selected_target' => $data['selected_target'] ? true : false,
        ))) ?>

        <div class="pager1">
            <p><strong>件数計算中...</strong></p>
        </div>

        <?php write_html($this->parseTemplate('UserListItemCount.php', array(
            'limit'         => $data['list_page']['limit'],
        ))) ?>
    <!-- /.userList --></section>

