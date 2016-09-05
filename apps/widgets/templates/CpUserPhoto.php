<?php write_html($this->formHidden('photo_edit_modal_url', Util::rewriteUrl('admin-cp', 'api_get_photo_edit_modal.json'))) ?>
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
                    <?php assign(Util::cutTextByWidth($data['cp_photo_action']->title, 750)); ?>
                </th>
            </tr>
            <tr>
                <th class="jsAreaToggleWrap" >写真</th>
                <?php if($data['cp_photo_action']->title_required): ?>
                    <th class="jsAreaToggleWrap">タイトル</th>
                <?php endif; ?>
                <?php if($data['cp_photo_action']->comment_required): ?>
                    <th class="jsAreaToggleWrap">コメント</th>
                <?php endif; ?>
                <?php if($data['cp_photo_action']->fb_share_required || $data['cp_photo_action']->tw_share_required): ?>
                    <th class="jsAreaToggleWrap snsAccount">
                        シェアSNS
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS . '/' . $data['display_action_id']] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchPhotoShareSns.php', array(
                            'search_photo_share_sns' => $data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS . '/' . $data['display_action_id']],
                            'action_id'              => $data['display_action_id']
                        ))) ?>
                    </th>
                    <th class="jsAreaToggleWrap snsAccount">
                        シェアテキスト
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_SHARE_TEXT . '/' . $data['display_action_id']] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchPhotoShareText.php', array(
                            'search_photo_share_text' => $data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_SHARE_TEXT . '/' . $data['display_action_id']],
                            'action_id'               => $data['display_action_id']
                        ))) ?>
                    </th>
                <?php endif; ?>
                <?php if($data['cp_photo_action']->panel_hidden_flg): ?>
                    <th class="jsAreaToggleWrap snsAccount">
                        検閲
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_APPROVAL_STATUS . '/' . $data['display_action_id']] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchPhotoApprovalStatus.php', array(
                            'search_photo_approval_status' => $data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_APPROVAL_STATUS . '/' . $data['display_action_id']],
                            'action_id'                    => $data['display_action_id']
                        ))) ?>
                    </th>
                <?php endif; ?>
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

                <?php $photo_user = $data['user_photo_array'][$fan_list_user->cp_user_id] ;?>
                <?php if($photo_user): ?>
                    <td>
                        <a href="#photo_edit_modal" class="jsOpenPhotoModal" data-photo_user_id="<?php assign($photo_user['id']) ?>" data-page_type=<?php assign('show_user_list') ?>>
                            <img src="<?php assign($photo_user['photo_url']) ?>" alt="" data-photo_user_id="<?php assign($photo_user['id']) ?>" class="postPreview"/>
                        </a>
                    </td>
                    <?php if($data['cp_photo_action']->title_required): ?>
                        <td title="<?php assign($photo_user['photo_title']) ?>"><?php assign(Util::cutTextByWidth($photo_user['photo_title'], 190)) ?></td>
                    <?php endif; ?>
                    <?php if($data['cp_photo_action']->comment_required): ?>
                        <td title="<?php assign($photo_user['photo_comment']) ?>"><?php assign(Util::cutTextByWidth($photo_user['photo_comment'],190)) ?></td>
                    <?php endif; ?>
                    <?php if($data['cp_photo_action']->fb_share_required || $data['cp_photo_action']->tw_share_required): ?>
                    <td>
                        <span class="accountList">
                        <?php foreach($photo_user['social_media_type'] as $photo_user_share): ?>
                            <span class="icon<?php assign(SocialAccount::$socialMediaTypeIcon[$photo_user_share['social_media_type']])?>2"><?php assign(SocialAccount::$socialMediaTypeName[$photo_user_share['social_media_type']]) ?></span>
                        <?php endforeach; ?>
                        </span>
                    </td>
                    <?php endif; ?>
                    <?php if($data['cp_photo_action']->fb_share_required || $data['cp_photo_action']->tw_share_required): ?>
                        <td title="<?php assign($photo_user['share_text'] ? $photo_user['share_text'] : '') ?>"><?php assign(Util::cutTextByWidth($photo_user['share_text'] ? $photo_user['share_text'] : '', 190)); ?></td>
                    <?php endif; ?>
                    <?php if($data['cp_photo_action']->panel_hidden_flg): ?>
                        <td><?php assign($photo_user['approval_status']) ?></td>
                    <?php endif; ?>
                <?php else: ?>
                    <td></td>
                    <?php if($data['cp_photo_action']->title_required): ?>
                        <td></td>
                    <?php endif; ?>
                    <?php if($data['cp_photo_action']->comment_required): ?>
                        <td></td>
                    <?php endif; ?>
                    <?php if($data['cp_photo_action']->fb_share_required || $data['cp_photo_action']->tw_share_required): ?>
                        <td></td>
                        <td></td>
                    <?php endif; ?>
                    <?php if($data['cp_photo_action']->panel_hidden_flg): ?>
                        <td></td>
                    <?php endif; ?>
                <?php endif; ?>
                </tr>
            <?php endforeach; ?>

            <?php for($i = 1; $i <= CpCreateSqlService::DISPLAY_20_ITEMS-count($data['fan_list_users']); $i++): ?>
                <tr>
                    <td></td>
                    <td></td>
                    <?php if($data['cp_photo_action']->title_required): ?>
                        <td></td>
                    <?php endif; ?>
                    <?php if($data['cp_photo_action']->comment_required): ?>
                        <td></td>
                    <?php endif; ?>
                    <?php if($data['cp_photo_action']->fb_share_required || $data['cp_photo_action']->tw_share_required): ?>
                        <td></td>
                        <td></td>
                    <?php endif; ?>
                    <?php if($data['cp_photo_action']->panel_hidden_flg): ?>
                        <td></td>
                    <?php endif; ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    <!-- /.itemTable --></table>
<!-- /.itemTableWrap --></div>
