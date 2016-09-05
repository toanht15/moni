<?php $page_name = $data->social_app_id == SocialApps::PROVIDER_TWITTER ? $data->name . '@' . $data->screen_name : $data->name; ?>
<header class="snsPageHeader">
    <figure><img src="<?php assign($data->picture_url); ?>" width="40" height="40" alt=""></figure>
    <div class="accountWrap">
        <?php if ($data->social_app_id == SocialApps::PROVIDER_FACEBOOK): ?>
            <h1><a href="https://www.facebook.com/<?php assign($data->social_media_account_id); ?>" target="_blank"><?php assign($page_name); ?></a><span class="labelFb">Facebook</span></h1>
            <div class="snsBtnWrap"><div class="fb-like" data-href="https://www.facebook.com/<?php assign($this->brand_social_account->social_media_account_id); ?>" data-layout="button" data-action="like" data-show-faces="false" data-share="false"></div></div>
        <?php elseif ($data->social_app_id == SocialApps::PROVIDER_TWITTER): ?>
            <h1><a href="https://twitter.com/<?php assign($data->screen_name); ?>" target="_blank"><?php assign($page_name); ?></a><span class="labelTw">Twitter</span></h1>
            <div class="snsBtnWrap"><a href="https://twitter.com/<?php assign($data->screen_name); ?>" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @twitter</a></div>
        <?php elseif ($data->social_app_id == SocialApps::PROVIDER_GOOGLE): ?>
            <h1><a href="https://www.youtube.com/channel/<?php assign(json_decode($data->store)->channelId); ?>" target="_blank"><?php assign($page_name); ?></a><span class="labelYt">YouTube</span></h1>
        <?php elseif ($data->social_app_id == SocialApps::PROVIDER_INSTAGRAM): ?>
            <h1><a href="//instagram.com/<?php assign($data->name)?>" target="_blank"><?php assign($page_name); ?></a></h1>
            <div class="snsBtnWrap"><a href="//instagram.com/<?php assign($data->name)?>?ref=badge" class="ig-b- ig-b-v-24" target="_blank"><img src="//badges.instagram.com/static/images/ig-badge-view-24.png" alt="Instagram" /></a></div>
        <?php endif; ?>

        <?php if ($data->social_app_id == SocialApps::PROVIDER_INSTAGRAM && json_decode($data->store)->bio): ?>
            <p class="aboutAccount"><?php assign(json_decode($data->store)->bio); ?></p>
        <?php elseif ($data->about): ?>
            <p class="aboutAccount"><?php assign($data->about); ?></p>
        <?php endif; ?>
        <!-- /.accountWrap --></div>
    <!-- /.snsPageHeader --></header>