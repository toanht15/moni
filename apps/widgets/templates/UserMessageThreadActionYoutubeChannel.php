<section class="message_engagement jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>">
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeYoutubeChannelActionForm"
          action="<?php assign(Util::rewriteUrl('messages', "api_execute_youtube_channel_action.json")); ?>"
          method="POST">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
        <?php write_html($this->formHidden('channel_id', $data['channel_id'])); ?>

        <h1 class="messageHd1">「<?php assign($data['target_account']->name ? : 'アカウント名'); ?>」のYouTubeチャンネルを登録しよう！</h1>
        <div class="engagementInner">
            <figure><img src="<?php assign($data['target_account']->picture_url); ?>" alt="<?php assign($data['target_account']->name); ?>" width="65" height="65"></figure>
            <p class="engagementYt">
                <?php if($data['view_status'] == UserMessageThreadActionYoutubeChannel::VIEW_FOLLOWED): ?>
                    <span class="btnYtFollow"><span>登録済み</span></span><br>
                <?php else: ?>
                    <span class="btnYtFollow" id="btnYtUnFollowed"><a href="<?php assign(Util::rewriteUrl(
                            'auth', 'google_connect',
                            array(),
                            array('callback_url' => $data['callback_url'],
                                'cp_action_id' => $data['message_info']["cp_action"]->id))); ?>">チャンネル登録</a></span><br>
                <?php endif; ?>
                <?php if ($data['yt_api_error']) write_html('<span class="iconError1">失敗しました。再度お試し下さい。</span><br>'); ?>
                <strong><?php assign($data['target_account']->name); ?></strong><br>
                <?php if ($data['cp_user']->join_sns != SocialAccountService::SOCIAL_MEDIA_GOOGLE): ?>
                    <small>※Googleアカウントへのログインが求められます</small>
                <?php endif; ?>
            <!-- /.engagementYt --></p>
        <!-- /.engagementInner --></div>
        <?php if($data['message_info']["concrete_action"]->intro_flg): ?>
            <div class="demoMovie">
                <p><iframe src="https://www.youtube.com/embed/<?php assign($data['target_entry']->object_id);?>?rel=0" type="text/html" frameborder="0" allowfullscreen></iframe></p>
            <!-- /.demoMovie --></div>
        <?php endif; ?>
        <div class="messageFooter">
            <?php if($data['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN): ?>
                <p class="skip" id="skip_execute_youtube_channel_action"><a href="javascript:void(0)"><small>チャンネル登録せず次へ</small></a></p>
            <?php endif; ?>
        <!-- /.messageFooter --></div>
        <?php if($data['view_status'] == UserMessageThreadActionYoutubeChannel::VIEW_EXECUTE): ?>
            <div class="cmd_execute_youtube_channel_action"></div>
        <?php endif; ?>
    </form>
<!-- /.message --></section>
<?php write_html($this->scriptTag("user/UserActionYoutubeChannelService")); ?>