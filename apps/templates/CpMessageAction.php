<?php if ($data['message_info']['action_status']->isNotJoin() &&
         !$data['message_info']['cp_action']->isActive(CpInfoContainer::getInstance()->getCpById($data['cp_info']['cp']['id']))): ?>
    <!-- アクション未実行で、締め切り日を迎えた場合 -->
    <?php write_html($this->parseTemplate('UserMessageThreadActionDeadLine.php', [
            "message_info" => $data['message_info'],
        ]))
    ?>
<?php else: ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_ENTRY): ?>
        <?php write_html($this->parseTemplate('UserMessageThreadActionEntry.php', array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"]))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_MESSAGE): ?>
        <?php write_html($this->parseTemplate('UserMessageThreadActionMessage.php', array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"]))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_PHOTO): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionPhoto')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_QUESTIONNAIRE): ?>
        <?php write_html($this->parseTemplate('UserMessageThreadActionQuestionnaire.php', array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_ENGAGEMENT): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionEngagement')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"]))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_SHIPPING_ADDRESS): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionShippingAddress')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_FREE_ANSWER): ?>
        <?php write_html($this->parseTemplate('UserMessageThreadActionFreeAnswer.php', array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"]))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_JOIN_FINISH): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionJoinFinish')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus'], "cp_info"=>$data["cp_info"]))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_ANNOUNCE): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionAnnounce')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus'],'canSendAnnounceMail'=>true))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_INSTANT_WIN): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionInstantWin')->render(array('message_info' => $data['message_info'], 'cp_user' => $data['cp_user'], 'pageStatus' => $data['pageStatus'], 'cp_info' => $data['cp_info']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_COUPON): ?>
        <?php write_html($this->parseTemplate('UserMessageThreadActionCoupon.php', array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))); ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_MOVIE): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionMovie')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_SHARE): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionShare')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_TWITTER_FOLLOW): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionTwitterFollow')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_FACEBOOK_LIKE): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionFacebookLike')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_GIFT): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionGift')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], "pageStatus" => $data["pageStatus"]))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_INSTAGRAM_FOLLOW): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionInstagramFollow')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_TWEET): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionTweet')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_CODE_AUTHENTICATION): ?>
        <?php write_html($this->parseTemplate('UserMessageThreadActionCodeAuth.php', array('message_info' => $data["message_info"], 'cp_user' => $data['cp_user'], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_INSTAGRAM_HASHTAG): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionInstagramHashtag')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_RETWEET): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionRetweet')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_YOUTUBE_CHANNEL): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionYoutubeChannel')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_POPULAR_VOTE): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionPopularVote')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_CONVERSION_TAG): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionConversionTag')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>

    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_LINE_ADD_FRIEND): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionLineAddFriend')->render(array("message_info" => $data["message_info"], "cp_user" => $data["cp_user"], 'pageStatus' => $data['pageStatus']))) ?>
    <?php endif; ?>
    <?php if ($data["message_info"]["cp_action"]->type === CpAction::TYPE_PAYMENT): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('UserMessageThreadActionPayment')->render(array('message_info' =>  $data["message_info"], 'cp_user' => $data['cp_user'], 'cp_info' => $data['cp_info'], 'pageStatus' => $data['pageStatus']))); ?>
    <?php endif ?>

<?php endif ?>

<?php if ($data["is_last_cp_action_in_first_group"]): ?>
    <?php if($data['can_display_syn_next']):?>
        <?php write_html($this->parseTemplate('SynMenuNextButton.php', array('shown_monipla_media_link' => $data['shown_monipla_media_link'], 'message_info' => $data["message_info"]))) ?>
    <?php endif?>
    <div id="jsShowMoniplaPR">
        <script type="text/javascript">if (typeof(UserMessageThreadMoniplaPRService) !== 'undefined') { UserMessageThreadMoniplaPRService.showMoniplaPR();}</script>
    </div>
<?php endif; ?>

