<section class="messageWrap" id="socialButtons">

    <section class="message" id="socialButton_0">
        <ul class="btnSet">
            <li class="btn2"><a href="javascript:void(0)" class="middle1">次へ</a></li>
            <!-- /.btnSet --></ul>
        <p class="messageDate"><small><?php assign(date("Y/m/d H:i")); ?></small></p>
        <!-- /.message --></section>

    <?php foreach ($data['brand_social_accounts'] as $social_account): ?>

        <section class="message" style="display: none" id="socialButton_<?php assign($social_account->id) ?>">
            <?php if ($social_account->social_app_id == SocialApps::PROVIDER_FACEBOOK): ?>
                <div class="messageEngagement">
                    <h1>いいね！を押して「<?php assign($social_account->name); ?>」Facebookページを応援しよう！！</h1>
                    <div class="engagementInner jsFBButtonSP" style="text-align: center">
                        <figure style="display: inline-block; float: none; vertical-align: top; margin-bottom: 0px"><img src="<?php assign($social_account->picture_url); ?>" alt="<?php assign($social_account->name); ?>" width="65" height="65"></figure>
                        <div class="engagementFb_sp" style="display: inline-block; vertical-align: top;">
                            <div class="fb-like" data-href="<?php assign("https://facebook.com/pages/" . $social_account->name . "/" . $social_account->social_media_account_id); ?>" data-layout="box_count" data-action="like" data-show-faces="false" data-share="false"></div>
                            <!-- /.shareBtn_sp --></div>
                        <!-- /.shareInner --></div>
                    <div class="engagementInner jsFBButtonPC" style="display: none">
                        <figure style="margin-bottom: 0px"><img src="<?php assign($social_account->picture_url); ?>" alt="<?php assign($social_account->name); ?>" width="65" height="65"></figure>
                        <div class="engagementFb_pc" style="width: 300px">
                            <div class="fb-like" data-href="<?php assign("https://facebook.com/pages/" . $social_account->name . "/" . $social_account->social_media_account_id); ?>" data-width="435px" data-layout="standard" data-action="like" data-show-faces="true" data-share="false"></div>
                            <!-- /.shareBtn_pc --></div>
                        <!-- /.message_share --></div>
                    </div>
            <?php endif; ?>

            <?php if ($social_account->social_app_id == SocialApps::PROVIDER_TWITTER): ?>
                <div class="messageEngagement">
                    <h1 class="messageHd1">「<?php assign($social_account->name); ?>」Twitterアカウントをフォローしよう！</h1>
                    <div class="engagementInner">
                        <figure><img src="<?php assign($social_account->picture_url); ?>" alt="<?php assign($social_account->name); ?>"></figure>
                        <p class="engagementTw">
                        <span class="btnTwFollow"><a onclick="window.open('https://twitter.com/<?php assign($social_account->screen_name); ?>', 'title', 'width=1000,height=600');" href="javascript:void(0)" style="cursor: pointer;">フォローする</a></span><br>
                            <strong><?php assign($social_account->name); ?></strong><br>
                            <small>@<?php assign($social_account->screen_name); ?></small>
                        </p>
                        </div>
                </div>
                <p class="messageText"><?php assign($social_account->about); ?></p>
            <?php endif; ?>

            <?php if ($social_account->social_app_id == SocialApps::PROVIDER_GOOGLE): ?>
                <div style="text-align: center"><p>
                    <a href="//plus.google.com/u/0/<?php assign($social_account->social_media_account_id); ?>?prsrc=3"
                       rel="publisher" target="_top" style="text-decoration:none;">
                        <img src="//ssl.gstatic.com/images/icons/gplus-32.png" alt="Google+" style="border:0;width:32px;height:32px;"/>
                    </a>
                    <br><?php assign($social_account->name); ?>
                </p></div>
            <?php endif; ?>

            <div class="messageFooter">
                <?php if ($social_account->social_app_id != SocialApps::PROVIDER_FACEBOOK): ?>
                    <ul class="btnSet">
                        <li class="btn2"><a href="javascript:void(0)" class="middle1">次へ</a></li>
                        <!-- /.btnSet --></ul>
                <?php endif; ?>
                <p class="date"><small><?php assign(date("Y/m/d H:i")); ?></small></p>
            </div>
            <!-- /.message --></section>
    <?php endforeach; ?>
    <!-- /.messageWrap --></section>
