<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus']))?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<?php if ($data['action_type'] == CpAction::TYPE_QUESTIONNAIRE): ?>
    <?php write_html($this->parseTemplate('ImagePreviewModal.php')) ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_INSTAGRAM_HASHTAG): ?>
    <?php write_html($this->parseTemplate('InstagramHashtagUserPostModal.php')); ?>
<?php endif; ?>

<?php if ($data['action_type'] == CpAction::TYPE_POPULAR_VOTE): ?>
    <?php write_html($this->parseTemplate('CandidatePreviewModal.php')) ?>
<?php endif; ?>

<article>

<?php if($data['show_segment_message_action_alert']): ?>
    <div class="segmentPresetInfo">
        <p>セグメント機能からメッセージ作成中です</p>
        <!-- /.segmentPresetInfo --></div>
<?php endif; ?>

<?php write_html($this->parseTemplate('ActionHeader.php',array(
    'cp_id' => $data['cp_id'],
    'action_id' => $data['action_id'],
    'user_list_page' => true,
    'pageStatus' => $data['pageStatus'],
    'enable_archive' => false,
    'isHideDemoFunction' => false
))); ?>

<?php write_html($this->parseTemplate('CpUserListHeader.php',array(
    'cp_id' => $data['cp_id'],
    'action_id' => $data['action_id'],
    'current_page'=>$data['current_page'],
    'reservation' => $data['reservation'],
    'is_group_fixed' => $data['is_group_fixed'],
    'brand' => $data['brand'],
    'is_include_type_announce' => $data['is_include_type_announce'],
    'fixed_target' => $data['fixed_target']
))); ?>

<section class="campaignEditCont">
    <?php if(iterator_count($data['group_actions']) > 1):?>
    <ul class="tablink1">
        <?php foreach ($data['group_actions'] as $action): ?>
            <?php if ($action->id == $data['action_id']): ?>
                <li class="current"><span>Step <?php assign($data['min_step_no']+$action->order_no) ?>：<?php assign($action->getCpActionDetail()['title']) ?></span></li>
            <?php else: ?>
                <li><a href="<?php write_html(Util::rewriteUrl('admin-cp', 'edit_action', array($data['cp_id'], $action->id))) ?>">Step <?php assign($data['min_step_no']+$action->order_no) ?>：<?php assign($action->getCpActionDetail()['title']) ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <?php endif;?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('EditAction')->render(array('cp_id'=> $data['cp_id'],
    'action_id'=>$data['action_id'],
    'reservation' => $data['reservation'],
    'ActionForm' => $this->ActionForm,
    'ActionError'=>$this->ActionError,
    'pageStatus'=>$data['pageStatus'],))) ?>

<!-- /.campaignEditWrap --></section>

</article>

<script type="text/javascript" src="<?php assign($this->setVersion('/js/zeroclipboard/ZeroClipboard.min.js')) ?>"></script>
<?php $script = array(); $script[] = 'admin-cp/EditActionService'; ?>
<?php if ($data['action_type'] == CpAction::TYPE_BUTTONS): ?>
    <?php $script[] = 'admin-cp/EditButtonsActionService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_MESSAGE): ?>
    <?php $script[] = 'admin-cp/EditMessageActionService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_SHIPPING_ADDRESS): ?>
    <?php $script[] = 'admin-cp/EditShippingAddressActionService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_QUESTIONNAIRE): ?>
    <?php $script[] = 'admin-cp/EditActionQuestionnaireService'; ?>
<?php endif; ?>
<?php if($data['action_type'] == CpAction::TYPE_PHOTO): ?>
    <?php $script[] = 'admin-cp/EditPhotoActionService'; ?>
<?php endif ?>
<?php if ($data['action_type'] == CpAction::TYPE_FREE_ANSWER): ?>
    <?php $script[] = 'admin-cp/EditFreeAnswerActionService'; ?>
<?php endif ?>
<?php if ($data['action_type'] == CpAction::TYPE_ENGAGEMENT): ?>
    <?php $script[] = 'admin-cp/EditEngagementActionService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_INSTANT_WIN): ?>
    <?php $script[] = 'admin-cp/EditInstantWinActionService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_JOIN_FINISH || $data['action_type'] == CpAction::TYPE_ANNOUNCE): ?>
    <?php $script[] = 'admin-cp/EditMessageCommonService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_COUPON): ?>
    <?php $script[] = 'admin-cp/EditCouponActionService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_MOVIE): ?>
    <?php $script[] = 'admin-cp/EditMovieActionService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_TWITTER_FOLLOW): ?>
    <?php $script[] = 'admin-cp/EditTwitterFollowActionService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_SHARE): ?>
    <?php $script[] = 'admin-cp/EditShareActionService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_FACEBOOK_LIKE): ?>
    <?php $script[] = 'admin-cp/EditFacebookLikeActionService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_GIFT): ?>
    <?php $script[] = 'admin-cp/EditGiftActionService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_INSTAGRAM_FOLLOW): ?>
    <?php $script[] = 'admin-cp/EditInstagramFollowActionService'; ?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_TWEET): ?>
    <?php $script[] = 'admin-cp/EditTweetActionService';?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_CODE_AUTHENTICATION): ?>
    <?php $script[] = 'admin-cp/EditCodeAuthActionService' ?>
<?php endif ?>
<?php if ($data['action_type'] == CpAction::TYPE_INSTAGRAM_HASHTAG): ?>
    <?php $script[] = 'admin-cp/EditInstagramHashtagActionService' ?>
<?php endif ?>
<?php if ($data['action_type'] == CpAction::TYPE_RETWEET): ?>
    <?php $script[] = 'admin-cp/EditRetweetActionService';?>
<?php endif; ?>
<?php if ($data['action_type'] == CpAction::TYPE_YOUTUBE_CHANNEL): ?>
    <?php $script[] = 'admin-cp/EditYoutubeChannelActionService' ?>
<?php endif ?>
<?php if ($data['action_type'] == CpAction::TYPE_POPULAR_VOTE): ?>
    <?php $script[] = 'admin-cp/EditPopularVoteActionService' ?>
<?php endif ?>
<?php if ($data['action_type'] == CpAction::TYPE_CONVERSION_TAG): ?>
    <?php $script[] = 'admin-cp/EditConversionTagActionService' ?>
<?php endif ?>
<?php if ($data['action_type'] == CpAction::TYPE_LINE_ADD_FRIEND): ?>
    <?php $script[] = 'admin-cp/EditLineAddFriendActionService'; ?>
<?php endif; ?>

<?php write_html($this->parseTemplate('MessageDeliveryConfirmBox.php', array(
    'reservation' => $data['reservation'],
    'cp_id' => $data['cp_id'],
    'pageStatus' => $data['pageStatus'],
))) ?>
<?php write_html($this->parseTemplate('CpDownloadList.php', array(
    'brand_id' => $data['brand']->id,
    'cp_id' => $data['cp_id'],
    'pageStatus' => $data['pageStatus'],
))) ?>

<?php $script[] = 'admin-cp/CpMenuService' ?>

<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<?php $param['twitter_counter_flg'] = '1'; ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
