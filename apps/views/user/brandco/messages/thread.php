<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

<?php if ($data['hasQuestionnaire']): ?>
    <?php write_html($this->parseTemplate('ImagePreviewModal.php')) ?>
<?php endif; ?>
<?php if ($data['hasGift']): ?>
    <link rel="stylesheet" href="<?php assign_js($this->setVersion('/css/flexslider/flexslider.css'))?>">
    <script type="text/javascript" src="<?php assign_js($this->setVersion('/js/flexslider/jquery.flexslider-min.js'))?>"></script>
<?php endif;?>
<?php if ($data['hasInstagramHashtag']): ?>
    <?php write_html($this->parseTemplate('InstagramHashtagUserPostModal.php')) ?>
<?php endif; ?>
<?php if ($data['hasPopularVote']): ?>
    <?php write_html($this->parseTemplate('CandidatePreviewModal.php')) ?>
<?php endif; ?>

<article>
    <?php write_html($this->csrf_tag()); ?>

    <section class="messageWrap">
        <a id="pinAction" href="#newMessage"></a>

        <?php foreach ($data['message_info_list'] as $message_info): ?>
            <?php if (end($data['message_info_list']) === $message_info): ?>
                <a id="newMessage"></a>
                <a id="indicatorAnchor" data-device="<?php assign(Util::isSmartPhone() ? 'SP' : 'PC'); ?>" data-map_increment_gauge="[<?php assign(implode(',', $data['map_increment_gauge'])); ?>]" data-shown_monipla_media_link="<?php assign($data['shown_monipla_media_link']) ?>" data-completed="0"></a>
            <?php endif; ?>
            <?php if ($message_info['action_status']->isNotJoin() &&
                !$message_info['cp_action']->isActive(CpInfoContainer::getInstance()->getCpById($data['cp_id']))): ?>
                <!-- アクション未実行で、締め切り日を迎えた場合 -->
                <?php write_html($this->parseTemplate('UserMessageThreadActionDeadLine.php', array(
                    "cp_info" => $data["cp_info"],
                    "message_info" => $message_info,
                    "cp_user" => $data["cp_user"],
                    'pageStatus' => $data['pageStatus'])))
                ?>
            <?php else: ?>
                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_ENTRY): ?>
                    <?php write_html($this->parseTemplate('UserMessageThreadActionEntry.php', array(
                        "cp_info" => $data["cp_info"],
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"],
                        'pageStatus' => $data['pageStatus'],
                        'brands_users_relation_id' => $data['brands_users_relation_id'],
                        'ignore_prefill' => $data['ignore_prefill'],
                        'entry_questionnaire_data' => $data['entry_questionnaire_data'])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_MESSAGE): ?>
                    <?php write_html($this->parseTemplate('UserMessageThreadActionMessage.php', array(
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_PHOTO): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionPhoto')->render(array(
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"],
                        "pageStatus" => $data['pageStatus'])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_QUESTIONNAIRE): ?>
                    <?php write_html($this->parseTemplate('UserMessageThreadActionQuestionnaire.php', array(
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"],
                        'pageStatus' => $data['pageStatus'],
                        "cp_info" => $data["cp_info"],
                        "brands_users_relation_id" => $data['brands_users_relation_id'],
                        'ignore_prefill' => $data['ignore_prefill'],
                        'entry_questionnaire_data' => $data['entry_questionnaire_data'])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_SHIPPING_ADDRESS): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionShippingAddress')->render(array(
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"],
                        'pageStatus' => $data['pageStatus'])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_ENGAGEMENT): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionEngagement')->render(array(
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_FREE_ANSWER): ?>
                    <?php write_html($this->parseTemplate('UserMessageThreadActionFreeAnswer.php', array(
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_JOIN_FINISH): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionJoinFinish')->render(array(
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"],
                        'pageStatus' => $data['pageStatus'],
                        "cp_info" => $data["cp_info"])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_ANNOUNCE): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionAnnounce')->render(array(
                            "message_info" => $message_info,
                            "cp_user" => $data["cp_user"],
                            'pageStatus' => $data['pageStatus'],
                            'canSendAnnounceMail' => false)
                    ))
                    ?>
                <?php endif; ?>
                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_COUPON): ?>
                    <?php write_html($this->parseTemplate('UserMessageThreadActionCoupon.php', array(
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"],
                        'pageStatus' => $data['pageStatus'])));
                    ?>
                <?php endif; ?>
                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_INSTANT_WIN): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionInstantWin')->render(array(
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"],
                        'pageStatus' => $data['pageStatus'],
                        "cp_info" => $data["cp_info"])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_MOVIE): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionMovie')->render(array(
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_TWITTER_FOLLOW): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionTwitterFollow')->render(array(
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"],
                        "pageStatus" => $data["pageStatus"])));
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_SHARE): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionShare')->render(array(
                        "message_info" => $message_info,
                        "cp_user" => $data["cp_user"],
                        "pageStatus" => $data['pageStatus'])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_FACEBOOK_LIKE): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionFacebookLike')->render(array(
                        'message_info' => $message_info,
                        'cp_user' => $data['cp_user'],
                        'pageStatus' => $data['pageStatus'],
                        'cp_info' => $data["cp_info"]
                    )));
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_GIFT): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionGift')->render(array(
                        'message_info' => $message_info,
                        'cp_user' => $data['cp_user'],
                        'pageStatus' => $data['pageStatus'])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info["cp_action"]->type === CpAction::TYPE_INSTAGRAM_FOLLOW): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionInstagramFollow')->render(array(
                        'message_info' => $message_info,
                        'cp_user' => $data['cp_user'],
                        'pageStatus' => $data['pageStatus']
                    )));
                    ?>
                <?php endif; ?>

                <?php if ($message_info['cp_action']->type === CpAction::TYPE_TWEET): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionTweet')->render(array(
                        'message_info' => $message_info,
                        'cp_user' => $data['cp_user'],
                        'pageStatus' => $data['pageStatus'])));
                    ?>
                <?php endif; ?>

                <?php if ($message_info['cp_action']->type === CpAction::TYPE_CODE_AUTHENTICATION): ?>
                    <?php write_html($this->parseTemplate('UserMessageThreadActionCodeAuth.php', array(
                        'message_info' => $message_info,
                        'cp_user' => $data['cp_user'],
                        'pageStatus' => $data['pageStatus'])))
                    ?>
                <?php endif ?>

                <?php if ($message_info['cp_action']->type === CpAction::TYPE_INSTAGRAM_HASHTAG): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionInstagramHashtag')->render(array(
                        'message_info' => $message_info,
                        'cp_user' => $data['cp_user'],
                        'pageStatus' => $data['pageStatus'])))
                    ?>
                <?php endif; ?>

                <?php if ($message_info['cp_action']->type === CpAction::TYPE_RETWEET): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionRetweet')->render(array(
                        'message_info' => $message_info,
                        'cp_user' => $data['cp_user'],
                        'pageStatus' => $data['pageStatus'])));
                    ?>
                <?php endif; ?>

                <?php if ($message_info['cp_action']->type === CpAction::TYPE_YOUTUBE_CHANNEL): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionYoutubeChannel')->render(array(
                        'message_info' => $message_info,
                        'cp_user' => $data['cp_user'],
                        'pageStatus' => $data['pageStatus'])));
                    ?>
                <?php endif ?>

                <?php if ($message_info['cp_action']->type === CpAction::TYPE_POPULAR_VOTE): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionPopularVote')->render(array(
                        'message_info' => $message_info,
                        'cp_user' => $data['cp_user'],
                        'pageStatus' => $data['pageStatus']
                    ))); ?>
                <?php endif ?>

                <?php if ($message_info['cp_action']->type === CpAction::TYPE_CONVERSION_TAG): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionConversionTag')->render(array(
                        'message_info' => $message_info,
                        'cp_user' => $data['cp_user'],
                        'pageStatus' => $data['pageStatus']
                    ))); ?>
                <?php endif ?>

                <?php if ($message_info['cp_action']->type === CpAction::TYPE_LINE_ADD_FRIEND): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionLineAddFriend')->render(array(
                        'message_info' => $message_info,
                        'cp_user' => $data['cp_user'],
                        'pageStatus' => $data['pageStatus']
                    ))); ?>
                <?php endif ?>

                <?php if ($message_info['cp_action']->type === CpAction::TYPE_PAYMENT): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionPayment')->render(array(
                        'message_info' => $message_info,
                        'cp_user' => $data['cp_user'],
                        'cp_info' => $data['cp_info'],
                        'pageStatus' => $data['pageStatus']
                    ))); ?>
                <?php endif ?>

                <?php // モニプラ導線の表示: 新しいモジュールを追加するときは上に記述してください ?>
                <?php // 第1グループの最後に表示させる ?>
                <?php if ($message_info['cp_action']->id === $data['last_cp_action_in_first_group']->id): ?>
                    <?php if($data['can_display_syn_next']):?>
                        <?php write_html($this->parseTemplate('SynMenuNextButton.php', array(
                            'shown_monipla_media_link' => $data['shown_monipla_media_link'],
                            'message_info' => $message_info
                        ))) ?>
                    <?php endif;?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>

        <div id="jsShowMoniplaPR">
            <script type="text/javascript">if (typeof(UserMessageThreadMoniplaPRService) !== 'undefined') { UserMessageThreadMoniplaPRService.showMoniplaPR();}</script>
        </div>

        <section class="barIndicatorWrap" style="display: none;">
            <input type="hidden" name="monipla_media_url" value="<?php assign(config('Protocol.Secure') . '://' . config('Domain.monipla_media').'/login'); ?>">
            <input type="hidden" name="tracker_name" value="<?php assign(config('Analytics.TrackerName')); ?>">
            <input type="hidden" name="cp_id" value="<?php assign($data['cp_id']); ?>">
            <input type="hidden" name="page_url" value="<?php assign($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>">
            <div class="barIndicatorInner jsIndicatorInProgress">
                <h1>参加完了まで</h1>
                <p class="barIndicatorCount"><span class="barIndicator"><!-- /.barIndicator --></span></p>
                <!-- /.barIndicatorInner --></div>
            <!-- /.barIndicatorWrap --></section>

        <!-- /.messageWrap --></section>
</article>

<?php if (config('Stage') === 'product'): ?>
    <span class="jsGoogleAnalyticsTrackingAction"
          data-product='{"id": "P<?php assign($data['cp_id']); ?>", "name": "campaign_<?php assign($data['cp_id']); ?>"}'
          data-action="add"></span>
<?php endif ?>

<?php write_html($this->formHidden('cp_id', $data['cp_id'])) ?>
<?php write_html($this->formHidden('user_id', $data['user_id'])) ?>
<?php write_html($this->formHidden('base_url', Util::getBaseUrl(true))) ?>

<?php write_html($this->formHidden('isSP',Util::isSmartPhone()));?>
<?php $script = array('user/CpIndicatorService', 'user/UserMessageThreadService', 'user/UserMessageThreadMoniplaPRService'); ?>

<?php $param = array_merge($data['pageStatus'], array('script' => $script, 'extend_tag' => $data['cp_info']['cp']['extend_tag'])) ?>
<p id="loading" class="loading" style="display:none">
    <img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" alt="loading">
    <!-- /.loading --></p>
<script src="<?php assign($this->setVersion('/js/jquery.inview.min.js'))?>"></script>

<script src="<?php assign($this->setVersion('/js/bi/jquery-barIndicator.js'))?>"></script>
<script src="<?php assign($this->setVersion('/js/jquery.easing.1.3.js'))?>"></script>

<?php $param['twitter_counter_flg'] = '1'; ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
