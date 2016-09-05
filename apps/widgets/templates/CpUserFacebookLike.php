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
            <th class="jsAreaToggleWrap">
                <?php assign(Util::cutTextByWidth($data['cp_facebook_like_action']->title, 750)); ?>
            </th>
        </tr>
        <tr>
            <th class="jsAreaToggleWrap" title="Facebookいいね！">
                Facebookいいね！状況
                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_FB_LIKE_TYPE . '/' . $data['cp_facebook_like_action']->cp_action_id] ? 'iconBtnSort' : 'btnArrowB1'); ?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchFbLikeType.php', array(
                    'search_fb_like' => $data['search_condition'][CpCreateSqlService::SEARCH_FB_LIKE_TYPE . '/' . $data['cp_facebook_like_action']->cp_action_id],
                    'action_id' => $data['cp_facebook_like_action']->cp_action_id
                ))) ?>
            </th>
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

            <?php if(isset($data['cp_facebook_like_statuses'][$fan_list_user->cp_user_id])): ?>
                <td><?php assign($data['cp_facebook_like_statuses'][$fan_list_user->cp_user_id]) ?></td>
            <?php else: ?>
                <td></td>
            <?php endif; ?>
            </tr>
        <?php endforeach; ?>

        <?php for($i = 1; $i <= 20-count($data['fan_list_users']); $i++): ?>
            <tr>
                <td></td>
                <td></td>
            </tr>
        <?php endfor; ?>
        </tbody>
        <!-- /.itemTable --></table>
    <!-- /.itemTableWrap --></div>
