<section class="jsMessage inview" id="message_<?php assign($data['message_info']["message"]->id); ?>">
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeFollowActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_twitter_follow_action.json")); ?>" method="POST" enctype="multipart/form-data">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
        <?php write_html($this->formHidden('concrete_action_id', $data['message_info']["concrete_action"]->id)); ?>

        <?php if (!$data['is_skip_action']): ?>
            <?php if ($data['follow_1'] || $data['follow_2']): ?>
                <section class="message_engagement">
                    <?php if ($data['brand_social_account']->social_app_id == SocialApps::PROVIDER_TWITTER): ?>
                        <h1 class="messageHd1"> <?php assign($data['title']); ?></h1>
                        <div class="engagementInner">
                            <figure><img src="<?php assign($data['brand_social_account']->picture_url); ?>" alt="<?php assign($data['brand_social_account']->name); ?>" width="65" height="65"></figure>
                            <p class="<?php assign($data['follow_2'] || $data['follow_4_2'] ? 'engagementTw_already' : 'engagementTw'); ?>">
                                <?php if ($data['follow_2'] || $data['follow_4_2']) : ?>
                                    <span>既にフォロー済みです。</span>
                                <?php endif; ?>
                                <strong class="fangateName"><?php assign($data['brand_social_account']->name); ?></strong><br>
                                <small>@<?php assign($data['brand_social_account']->screen_name); ?></small>
                                <!-- /.engagementTw --></p>
                            <!-- /.engagementInner --></div>
                    <?php endif; ?>

                    <div class="messageFooter">
                        <ul class="btnSet">
                            <?php if ($data['follow_2'] || $data['follow_4_2']): ?>
                                <li class="btnTwFollow"><a href="javascript:void(0)" class="large1 cmd_execute_follow_skip_action">フォローして次へ</a></li>
                            <?php elseif ($data['follow_5']): ?>
                                <li class="btnTwFollow">
                                    <a class="large1 cmd_execute_connect_account jsExecuteAction" href="javascript:void(0);"
                                       data-redirect_url="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'tw', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['cp_user']->cp_id))) ?>"
                                       data-pre_execute_url="<?php assign(Util::rewriteUrl('messages', "api_pre_execute_twitter_follow_action.json")); ?>" data-need_update_token="<?php assign($data['access_token_valid'] ? 0 : 1 )?>"><?php assign($data['is_last_action'] ? 'フォローする' : 'フォローして次へ'); ?></a>
                                </li>
                            <?php else: ?>
                                <li class="btnTwFollow"><a class="large1 cmd_execute_follow_action jsExecuteAction" href="javascript:void(0);"><?php assign($data['is_last_action'] ? 'フォローする' : 'フォローして次へ'); ?></a></li><br>
                            <?php endif ?>
                            <!-- /.btnSet --></ul>
                        <?php if ($data['skip_flg']): ?>
                            <p class="skip"><a class="cmd_execute_follow_skip_action" href="javascript:void(0);"><small>フォローせず次へ</small></a></p>
                        <?php endif; ?>
                    </div>
                    <!-- /.message --></section>
            <?php endif; ?>

            <?php if ($data['follow_2']): ?>
                <!-- フォロー済み -->
                <div class="cmd_execute_follow_already_action" data-messageid="<?php assign($data['message_info']["message"]->id); ?>"></div>
            <?php endif; ?>
            <?php if ($data['follow_4_1']): ?>
                <!-- キャンペーンに参加し、Followモジュール実行済み(フォローしていない) -->
                <div class="cmd_execute_follow_close_action_exec" data-messageid="<?php assign($data['message_info']["message"]->id); ?>"></div>
            <?php endif; ?>
            <?php if ($data['follow_4_2']): ?>
                <!-- キャンペーンに参加し、Followモジュール実行済み(フォロー済み) -->
                <div class="cmd_execute_follow_close_action_already" data-messageid="<?php assign($data['message_info']["message"]->id); ?>"></div>
            <?php endif; ?>
            <?php if ($data['follow_4_5']): ?>
                <!-- SNS連携した後、自動フォロー実行 -->
                <div class="cmd_execute_follow_close_action_connecting" data-messageid="<?php assign($data['message_info']["message"]->id); ?>"></div>
            <?php endif ?>
            <?php if ($data['dead_line']): ?>
                <!-- 締め切り日に達している -->
                <div class="cmd_execute_dead_line" data-messageid="<?php assign($data['message_info']["message"]->id); ?>"></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($data['auto_skip_action']): ?>
            <div class="cmd_auto_execute_skip_twitter_follow_action" data-messageid="<?php assign($data['message_info']["message"]->id); ?>"></div>
        <?php endif; ?>

    </form>
</section>
<?php write_html($this->scriptTag('user/UserActionTwitterFolowService')); ?>


