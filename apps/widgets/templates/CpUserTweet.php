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
            <th class="jsAreaToggleWrap" colspan="4">
                <?php assign(Util::cutTextByWidth($data['cp_tweet_action']->title, 750)); ?>
            </th>
        </tr>
        <tr>
            <th class="snsAccount jsAreaToggleWrap" title="ツイート">
                ツイート状況
                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_TWEET_TYPE . '/' . $data['cp_tweet_action']->cp_action_id] ? 'iconBtnSort' : 'btnArrowB1'); ?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchTweetType.php', array(
                    'search_tweet' => $data['search_condition'][CpCreateSqlService::SEARCH_TWEET_TYPE . '/' . $data['cp_tweet_action']->cp_action_id],
                    'action_id' => $data['cp_tweet_action']->cp_action_id,
                    'tweet_types' => TweetMessage::$tweet_statuses
                ))) ?>
            </th>
            <th>ツイートURL</th>
            <th>ツイート内容</th>
            <th>写真</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data['fan_list_users'] as $fan_list_user): ?>
            <?php $tweet_message = $data['tweet_content_list']['tweet_message'][$fan_list_user->cp_user_id];?>
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
            <td><?php write_html($this->toHalfContentDeeply($tweet_message['tweet_status'] ? : ''))?></td>
            <td><?php if ($tweet_message['tweet_content_url']):?>
                    <a href="<?php assign($tweet_message['tweet_content_url'])?>" target="_blank"><?php assign(Util::cutTextByWidth($tweet_message['tweet_content_url'], 190))?></a>
                <?php endif;?>
            </td>
            <td><?php assign(Util::cutTextByWidth($tweet_message['tweet_text'] ? : '', 190)) ?></td>
            <td><?php if ($tweet_message['tweet_message_id']): ?>
                    <?php foreach ($data['tweet_content_list']['tweet_photo'][$tweet_message['tweet_message_id']] as $element) :?>
                        <a href="#view_tweet_photo_modal" class="jsOpenTweetPhotoModal" data-photo_url="<?php assign($element) ?>">
                            <img src="<?php assign($element) ?>" alt="" height="30"/>
                        </a>
                    <?php endforeach;?>
                <?php endif; ?>
            </td>
            </tr>
        <?php endforeach; ?>

        <?php for($i = 1; $i <= 20-count($data['fan_list_users']); $i++): ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php endfor; ?>
        </tbody>
        <!-- /.itemTable --></table>
    <!-- /.itemTableWrap --></div>
