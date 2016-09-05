<section class="jsMessage inview" id="message_<?php assign($data['message_info']["message"]->id); ?>">
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeFbLikeActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_facebook_like_action.json")); ?>" method="POST" enctype="multipart/form-data" data-cp-type="<?php assign($data['cp_info']['cp']['status']) ?>">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
        <?php write_html($this->formHidden('concrete_action_id', $data['message_info']["concrete_action"]->id)); ?>
        <?php write_html($this->formHidden('brand_social_account_id', $data['brand_social_account']->id)); ?>
        <?php write_html($this->formHidden('fb_like_log_action_url', Util::rewriteUrl('messages', "api_execute_facebook_like_log_action.json"))); ?>
        <?php write_html($this->formHidden('sns_get_data_action_url', Util::rewriteUrl('messages', "api_execute_sns_get_data_action.json"))); ?>

        <?php if ($data['like_1']): ?>
            <section class="message">
                <?php if ($data['brand_social_account']->social_app_id == SocialApps::PROVIDER_FACEBOOK): ?>
                <div class="messageEngagement">
                    <h1><?php assign($data['title']); ?></h1>
                    <div class="engagementInner">
                        <figure><img src="<?php assign($data['brand_social_account']->picture_url); ?>" alt="<?php assign($data['brand_social_account']->name); ?>" width="65" height="65"></figure>
                        <?php if ($data['like_4_1'] || $data['like_4_2']): ?>
                            <div class="engagementFb_already">
                            <?php if ($data['like_4_1']): ?>
                                <p>いいね！を押していただきありがとうございました。</p>
                            <?php endif ?>
                            <?php if ($data['like_4_2']): ?>
                                <p>既に「いいね！」済みです。</p>
                            <?php endif ?>
                            </div>
                        <?php else: ?>
                            <div class="engagementFb_already" id="like_1_already" style="display: none">
                                <p>既に「いいね！」済みです。</p>
                            </div>
                            <div class="engagementFb_already" id="like_1_action" style="display: none">
                                <p>いいね！を押していただきありがとうございました。</p>
                            </div>
                            <div class="engagementFb_already" id="dead_line" style="display: none">
                                <p>キャンペーンを締め切りました。</p>
                            </div>
                            <div class="engagementFb_pc">
                                <div class="fb-like" data-href="<?php assign("https://facebook.com/pages/" . $data['brand_social_account']->name . "/" . $data['brand_social_account']->social_media_account_id); ?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="false"></div>
                            <!-- /.engagementFb_pc --></div>
                            <div class="engagementFb_sp">
                                <div class="fb-like" data-href="<?php assign("https://facebook.com/pages/" . $data['brand_social_account']->name . "/" . $data['brand_social_account']->social_media_account_id); ?>" data-layout="box_count" data-action="like" data-show-faces="false" data-share="false"></div>
                            <!-- /.engagementFb_sp --></div>
                        <?php endif ?>
                    <!-- /.engagementInner --></div>
                <!-- /.messageEngagement --></div>
                <?php endif; ?>
                <div class="messageFooter">
                    <p class="skip cmd_execute_like_skip_action"><a href="javascript:void(0);"><small>いいね！せず次へ</small></a></p>
                </div>
            <!-- /.message --></section>
        <?php endif ?>

        <?php if ($data['like_0']): ?>
            <!-- Facebook未連携 -->
            <div class="cmd_execute_like_unread_action" data-messageid="<?php assign($data['message_info']["message"]->id); ?>"></div>
        <?php endif; ?>
        <?php if ($data['like_1'] && !$data['like_4']): ?>
            <!-- いいね済みチェック -->
            <div class="cmd_execute_like_check_action" data-messageid="<?php assign($data['message_info']["message"]->id); ?>"></div>
        <?php endif; ?>
        <?php if ($data['like_4']): ?>
            <!-- キャンペーンに参加し、いいねモジュール実行済み -->
            <div class="cmd_execute_like_close_action" data-messageid="<?php assign($data['message_info']["message"]->id); ?>"></div>
        <?php endif; ?>
        <?php if ($data['dead_line']): ?>
            <!-- 締め切り日に達している -->
            <div class="cmd_execute_dead_line_action" data-messageid="<?php assign($data['message_info']["message"]->id + 1); ?>"></div>
        <?php endif; ?>
    </form>
</section>
<?php write_html($this->scriptTag('user/UserActionFacebookLikeService')); ?>
