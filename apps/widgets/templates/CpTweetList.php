<?php $service_factory = new aafwServiceFactory();
$cp_flow_service = $service_factory->create('CpFlowService');
$manager_service = $service_factory->create('ManagerService');
$tweet_service = $service_factory->create('TweetMessageService');
?>

<form method="POST" name="tweet_action_form" action="<?php assign(Util::rewriteUrl('admin-cp', 'update_multi_tweet_posts')) ?>">
    <div class="campaignPhotoSearch">
        <ul class="tablink1">
            <?php foreach($data['tweet_actions'] as $tweet_action): ?>
                <?php $min_step_no = $cp_flow_service->getMinOrderOfActionInGroup($tweet_action->cp_action_group_id); ?>
                <?php if ($tweet_action->id == $data['action_id']): ?>
                    <li class="current"><span>STEP <?php assign($min_step_no + $tweet_action->order_no) ?>：<?php assign($tweet_action->getCpActionData()->title) ?></span></li>
                <?php else: ?>
                    <li><a href="<?php write_html(Util::rewriteUrl('admin-cp', 'tweet_posts', array($tweet_action->id))) ?>">STEP <?php assign($min_step_no + $tweet_action->order_no) ?>：<?php assign($tweet_action->getCpActionData()->title) ?></a></li>
                <?php endif ?>
            <?php endforeach; ?>
            <!-- /.tablink1 --></ul>
        <?php if(!$manager_service->isAgentLogin()): ?>
            <div class="itemsSortingDetail">
                <dl>
                    <dt>検閲</dt>
                    <dd><?php write_html($this->formRadio('panel_hidden_flg', $data['cur_tweet_action']->panel_hidden_flg, array('class' => 'jsTweetPanelHiddenFlg'), array(CpTweetAction::PANEL_TYPE_HIDDEN => 'あり', CpTweetAction::PANEL_TYPE_AVAILABLE => 'なし<small>（検閲は外部サイトへの出力のみに反映され、Twitter上には検閲有無に関わらず投稿されます）</small>'), array(),'',false)); ?></dd>
                </dl>
                <p class="btnSet"><span class="btn3"><a href="javascript:void(0);" class="small1 jsTweetPanelHiddenConfirm">適用</a></span></p>
                <!-- /.itemsSortingDetail --></div>
        <?php endif ?>
        <div class="itemsSortingDetail">
            <dl>
                <dt>絞り込み</dt>
                <dd class="jsCheckToggleWrap">
                    <?php $is_public_checked = in_array(1, $data['tweet_status']) ?>
                    <?php // TODO たまにチェックボックスのテキストが非表示になるので、一旦formCheckBox使わないように ?>
                    <label><input type="checkbox" name="tweet_status[]" value="1" <?php if (is_array( $data['tweet_status'] ) && in_array( 1, $data['tweet_status'] )): ?>checked="checked"<?php endif ?> class="jsTweetStatus jsCheckToggle">アカウント公開</label>
                    <label><input type="checkbox" name="tweet_status[]" value="2" <?php if (is_array( $data['tweet_status'] ) && in_array( 2, $data['tweet_status'] )): ?>checked="checked"<?php endif ?> class="jsTweetStatus">アカウント非公開</label>
                    <label><input type="checkbox" name="tweet_status[]" value="3" <?php if (is_array( $data['tweet_status'] ) && in_array( 3, $data['tweet_status'] )): ?>checked="checked"<?php endif ?> class="jsTweetStatus">ツイート削除済</label>
                    <p class="attentionWrap jsCheckToggleTarget" <?php if (!$is_public_checked): ?>style="display: none;"<?php endif ?>>
                        <?php write_html($this->formCheckBox('approval_status', $data['approval_status'], array('class' => 'jsTweetApprovalStatus'), array('1' => '出力', '2' => '非出力'))) ?>
                    </p>
                </dd>
                <dt>並び替え</dt>
                <dd>
                    <?php write_html($this->formSelect('order_kind', $data['order_kind'] ? $data['order_kind'] : 1, array('class' => 'jsTweetOrderKind'), array('1' => '投稿順', '2' => 'ユーザーID順'))); ?>&nbsp;&nbsp;&nbsp;
                    <?php write_html($this->formRadio('order_type', $data['order_type'] ? $data['order_type'] : 1, array('class' => 'jsTweetOrderType'), array('1' => '[A-Z↓] 昇順', '2' => '[Z-A↑] 降順'))); ?></dd>
            </dl>
            <p class="btnSet"><span class="btn2"><a href="javascript:void(0);" class="small1 jsTweetSearchReset">リセット</a></span><span class="btn3"><a href="javascript:void(0);" class="small1 jsTweetSearchBtn">適用</a></span></p>
            <!-- /.itemsSortingDetail --></div>
        <!-- /.campaignPhotoSearch --></div>

    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoCpDataListPager')->render(array(
        'TotalCount' => $data['total_tweet_count'],
        'CurrentPage' => $data['page'],
        'Count' => $data['page_limited'],
    ))) ?>

    <?php write_html($this->parseTemplate('BrandcoTweetActionMenu.php',  array('menu_order' => '1'))) ?>

    <div class="outputApi">
        <p class="labelModeAllied">
            <span class="confirmed">出力<strong><?php assign($data['approved_tweet_count']) ?></strong>件</span>
            <?php if ($data['api_url'] != ''): ?>
                <span class="btn2 jsExportAPIBtn"><span class="large2">外部出力APIのURL作成</span></span>
                <span class="url jsExportAPIUrl">URL：<?php assign($data['api_url']) ?></span>
            <?php else: ?>
                <span class="btn2 jsExportAPIBtn"><a href="javascript:void(0);" class="large2 jsExportAPI">外部出力APIのURL作成</a></span>
                <span class="url jsExportAPIUrl">URL：なし</span>
            <?php endif ?>
            <!-- /.labelModeAllied --></p>
        <!-- /.outputApi --></div>

    <?php write_html($this->csrf_tag()); ?>
    <?php write_html($this->formHidden('multi_tweet_approval_status', TweetMessage::APPROVAL_STATUS_APPROVE)) ?>
    <?php write_html($this->formHidden('action_id', $data['action_id'])); ?>
    <?php write_html($this->formHidden('page', $data['page'])) ?>

    <ul class="campaignTweet">
        <?php $tweet_fixed_text = $data['cur_tweet_action']->tweet_fixed_text != "" ? "\r\n" . $data['cur_tweet_action']->tweet_fixed_text : "" ?>
        <?php if ($data['tweet_messages'] && $data['tweet_messages']->total() != 0): ?>
            <?php foreach($data['tweet_messages'] as $tweet_message): ?>
                <?php
                    $user = $tweet_message->getCpUser()->getUser();
                    $photos = $tweet_service->getTweetPhotos($tweet_message->id);
                ?>

                <li>
                    <span class="labels">
                        <input type="checkbox" class="jsTweetCheck" name="tweet_message_ids[]" value="<?php assign($tweet_message->id) ?>">
                        <span class="<?php assign($tweet_message->getTweetStatusClass()) ?>"><?php assign($tweet_message->getTweetStatus()) ?></span>
                        <span class="<?php assign($tweet_message->getApprovalStatusClass()) ?>"><?php assign($tweet_message->getApprovalStatus()) ?></span>
                    <!-- /.labels --></span><span class="userData">
                        <?php if ($tweet_message->tweet_status == TweetMessage::TWEET_STATUS_PUBLIC): ?>
                            <a href="<?php assign($tweet_message->tweet_content_url) ?>" target="_blank">
                        <?php endif ?>
                            <span class="user">
                              <img src="<?php assign($user->profile_image_url); ?>" width="30" height="30" alt="<?php assign($user->profile_image_url); ?>" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';">
                              <span class="userMeta">
                                <span class="userName"><?php assign($this->cutLongText($user->name, 15)); ?></span>
                              <!-- /.userMeta --></span>
                            </span>
                            <span class="tweet"><?php assign($tweet_message->tweet_text . $tweet_fixed_text); ?></span>
                            <?php if ($photos): ?>
                                <span class="thumb">
                                    <?php foreach ($photos as $photo): ?>
                                        <span><img src="<?php assign($photo->image_url) ?>" alt="<?php assign($this->cutLongText($user->name, 15)); ?>" onerror="this.src='<?php assign($this->setVersion('/img/dummy/01.png'));?>';"></span>
                                    <?php endforeach ?>
                                </span>
                            <?php endif ?>
                            <span class="post"><?php assign(date('Y/m/d H:i', strtotime($tweet_message->created_at))); ?></span></a>
                        <?php if ($tweet_message->tweet_status == TweetMessage::TWEET_STATUS_PUBLIC): ?>
                            </a>
                        <?php endif ?>
                    </span>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <!-- /.campaignPhoto --></ul>

    <?php write_html($this->parseTemplate('BrandcoTweetActionMenu.php', array('menu_order' => '2'))) ?>

    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoCpDataListPager')->render(array(
        'TotalCount' => $data['total_tweet_count'],
        'CurrentPage' => $data['page'],
        'Count' => $data['page_limited'],
    ))) ?>
</form>
