<?php
$brand_social_account = $data['engagement_social_account']->getBrandSocialAccount();
?>

<section class="message jsMessage inview" id="message_<?php assign($data['message_info']["message"]->id); ?>">
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <div style="display: none" id="facebookAppId"><?php assign(aafwApplicationConfig::getInstance()->query('@facebook.Admin.AppId')); ?></div>

    <form class="executeEngagementActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_engagement_action.json")); ?>" method="POST" enctype="multipart/form-data" >
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
        <?php write_html($this->formHidden('brand_social_account_id', $brand_social_account->id)); ?>
        <?php write_html($this->formHidden('engagement_log_action_url', Util::rewriteUrl('messages', "api_execute_fb_like_action.json"))); ?>
        <?php write_html($this->formHidden('sns_get_data_action_url', Util::rewriteUrl('messages', "api_execute_sns_get_data_action.json"))); ?>

        <?php if ($brand_social_account->social_app_id == SocialApps::PROVIDER_FACEBOOK): ?>
            <div class="messageEngagement">
                <h1>いいね！を押して「<?php assign($brand_social_account->name); ?>」Facebookページを応援しよう！！</h1>
                <div class="engagementInner">
                    <figure><img src="<?php assign($brand_social_account->picture_url); ?>" alt="<?php assign($brand_social_account->name); ?>" width="65" height="65"></figure>
                    <div class="engagementFb_pc" id="fangateSns_pc_<?php assign($data['message_info']["message"]->id); ?>">
                        <div class="fb-like" data-href="<?php assign("https://facebook.com/pages/" . $brand_social_account->name . "/" . $brand_social_account->social_media_account_id); ?>" data-width="435px" data-layout="standard" data-action="like" data-show-faces="true" data-share="false"></div>
                        <!-- /.shareBtn_pc --></div>
                    <div class="engagementFb_sp" id="fangateSns_sp_<?php assign($data['message_info']["message"]->id); ?>">
                        <div class="fb-like" data-href="<?php assign("https://facebook.com/pages/" . $brand_social_account->name . "/" . $brand_social_account->social_media_account_id); ?>" data-layout="box_count" data-action="like" data-show-faces="false" data-share="false"></div>
                        <!-- /.shareBtn_sp --></div>
                    <!-- /.message_share --></div>
                <!-- /.message_share --></div>
        <?php else: ?>
            <?php if ($brand_social_account->social_app_id == SocialApps::PROVIDER_TWITTER): ?>
                <div class="messageEngagement">
                    <h1 class="messageHd1">「<?php assign($brand_social_account->name); ?>」Twitterアカウントをフォローしよう！</h1>
                    <div class="engagementInner">
                        <figure><img src="<?php assign($brand_social_account->picture_url) ?>" alt="<?php assign($brand_social_account->name); ?>" width="65" height="65"></figure>
                        <p class="engagementTw">
                            <span class="btnTwFollow"><a onclick="window.open('https://twitter.com/<?php assign($brand_social_account->screen_name) ?>', 'title', 'width=1000,height=600');" style="cursor: pointer;">フォローする</a></span><br>
                            <strong><?php assign($brand_social_account->name); ?></strong><br>
                            <small>@<?php assign($brand_social_account->screen_name); ?></small>
                        </p>
                    </div>
                <!-- /.fangate --></div>
                <p class="messageText">
                    <?php assign($brand_social_account->about); ?>
                </p>
            <?php endif; ?>

            <?php if ($brand_social_account->social_app_id == SocialApps::PROVIDER_GOOGLE): ?>
                <div style="text-align: center;">
                    <p>
                        <a href="//plus.google.com/u/0/<?php assign($brand_social_account->social_media_account_id); ?>?prsrc=3"
                           rel="publisher" target="_top" style="text-decoration:none;">
                            <img src="//ssl.gstatic.com/images/icons/gplus-32.png" alt="Google+" style="border:0;width:32px;height:32px;"/>
                        </a>
                        <br><?php assign($brand_social_account->name); ?>
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <ul class="btnSet" <?php if ($brand_social_account->social_app_id == SocialApps::PROVIDER_FACEBOOK): ?>style="display: none"<?php endif; ?> data-socialappid="<?php assign($brand_social_account->social_app_id); ?>">
            <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN) : ?>
                <li class="btn2"><a href="javascript:void(0)" class="cmd_execute_engagement_action small1" data-messageid="<?php assign($data['message_info']["message"]->id); ?>">次へ</a></li>
            <?php else: ?>
                <li class="btn2"><span class="small1" data-messageid="<?php assign($data['message_info']["message"]->id); ?>">次へ</span></li>
            <?php endif; ?>
            <!-- /.btnSet --></ul>

        <p class="messageDate"><small><?php assign($data["message_info"]["message"]->created_at); ?></small></p>
    </form>
</section>

<?php write_html($this->scriptTag('user/UserActionEngagementService')); ?>