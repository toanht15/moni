<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<?php write_html($this->parseTemplate('CpPublicConditions.php', array('cp_id' => $data['cp_id']))) ?>

<?php if ($data['cp_action']->type == CpAction::TYPE_QUESTIONNAIRE): ?>
    <?php write_html($this->parseTemplate('ImagePreviewModal.php')) ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_INSTAGRAM_HASHTAG): ?>
    <?php write_html($this->parseTemplate('InstagramHashtagUserPostModal.php')); ?>
<?php endif; ?>

<?php if ($data['cp_action']->type == CpAction::TYPE_POPULAR_VOTE): ?>
    <?php write_html($this->parseTemplate('CandidatePreviewModal.php')) ?>
<?php endif; ?>

<article>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('CreateCpActionHeader')->render(array('cp_id' => $data['cp_id'], 'current_id'=>$data['action_id'], 'mid'=>$this->params['mid']))) ?>

    <?php write_html(aafwWidgets::getInstance()->loadWidget('EditActionBase')->render(array('cp_id'=> $data['cp_id'], 'ActionForm' => $this->ActionForm, 'ActionError'=>$this->ActionError, 'action_id'=>$data['action_id'], 'pageStatus'=>$data['pageStatus']))) ?>

    <?php write_html(aafwWidgets::getInstance()->loadWidget('CreateCpActionFooter')->render(array('cp_id' => $data['cp_id'], 'action_id'=>$data['action_id']))) ?>
<!-- /.wrap --></article>

<script type="text/javascript" src="<?php assign($this->setVersion('/js/zeroclipboard/ZeroClipboard.min.js')) ?>"></script>
<?php $script = array(); $script[] = 'admin-cp/EditActionService'; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_BUTTONS): ?>
    <?php $script[] = 'admin-cp/EditButtonsActionService'; ?>
<?php elseif ($data['cp_action']->type == CpAction::TYPE_QUESTIONNAIRE): ?>
    <?php $script[] = 'admin-cp/EditActionQuestionnaireService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_MESSAGE): ?>
    <?php $script[] = 'admin-cp/EditMessageActionService'; ?>
<?php endif; ?>
<?php if($data['cp_action']->type == CpAction::TYPE_PHOTO): ?>
    <?php $script[] = 'admin-cp/EditPhotoActionService'; ?>
<?php endif ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_SHIPPING_ADDRESS): ?>
    <?php $script[] = 'admin-cp/EditShippingAddressActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_FREE_ANSWER): ?>
    <?php $script[] = 'admin-cp/EditFreeAnswerActionService'; ?>
<?php endif ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_ENGAGEMENT): ?>
    <?php $script[] = 'admin-cp/EditEngagementActionService'; ?>
<?php endif ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_INSTANT_WIN): ?>
    <?php $script[] = 'admin-cp/EditInstantWinActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_JOIN_FINISH || $data['cp_action']->type == CpAction::TYPE_ANNOUNCE): ?>
    <?php $script[] = 'admin-cp/EditMessageCommonService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_COUPON): ?>
    <?php $script[] = 'admin-cp/EditCouponActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_MOVIE): ?>
    <?php $script[] = 'admin-cp/EditMovieActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_TWITTER_FOLLOW): ?>
    <?php $script[] = 'admin-cp/EditTwitterFollowActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_SHARE): ?>
    <?php $script[] = 'admin-cp/EditShareActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_FACEBOOK_LIKE): ?>
    <?php $script[] = 'admin-cp/EditFacebookLikeActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_GIFT): ?>
    <?php $script[] = 'admin-cp/EditGiftActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_INSTAGRAM_FOLLOW): ?>
    <?php $script[] = 'admin-cp/EditInstagramFollowActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_TWEET): ?>
    <?php $script[] = 'admin-cp/EditTweetActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_CODE_AUTHENTICATION): ?>
    <?php $script[] = 'admin-cp/EditCodeAuthActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_INSTAGRAM_HASHTAG): ?>
    <?php $script[] = 'admin-cp/EditInstagramHashtagActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_RETWEET): ?>
    <?php $script[] = 'admin-cp/EditRetweetActionService'; ?>
<?php endif;?>
<?php if ($data['cp_action']->type == CpAction::TYPE_YOUTUBE_CHANNEL): ?>
    <?php $script[] = 'admin-cp/EditYoutubeChannelActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_POPULAR_VOTE): ?>
    <?php $script[] = 'admin-cp/EditPopularVoteActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_CONVERSION_TAG): ?>
    <?php $script[] = 'admin-cp/EditConversionTagActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_LINE_ADD_FRIEND): ?>
    <?php $script[] = 'admin-cp/EditLineAddFriendActionService'; ?>
<?php endif; ?>
<?php if ($data['cp_action']->type == CpAction::TYPE_ENTRY): ?>
    <?php $script[] = 'admin-cp/EditEntryActionService'; ?>
<?php endif; ?>


<?php write_html($this->parseTemplate('MessageDeliveryConfirmBox.php', array(
    'reservation' => null,
    'cp_id' => $data['cp_id'],
    'pageStatus' => $data['pageStatus'],
))) ?>
<?php $script[] = 'admin-cp/CpMenuService'; ?>

<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<?php $param['twitter_counter_flg'] = '1'; ?>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
