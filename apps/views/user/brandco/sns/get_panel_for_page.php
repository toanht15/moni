<?php foreach($data['panel_list'] as $panel): ?>

    <?php if ($panel['streamName'] == 'TwitterStream'): ?>
        <section class="snsPageList jsPanel">
            <a href="<?php assign($panel['entry']['page_link']); ?>" class="twInner panelClick"
               data-link="<?php assign($panel['entry']['link']); ?>"
               data-entry="<?php assign(StreamService::STREAM_TYPE_TWITTER)?>"
               data-type="<?php assign(UserPanelClick::CATEGORY_PANEL); ?>">
                <p class="contText">
                    <?php assign($panel['entry']['panel_text']); ?>

                    <?php if ($panel['entry']['image_url']): ?>
                        <img src="<?php assign($panel['entry']['image_url']); ?>" width="285" height="241" alt="">
                    <?php endif; ?>

                    <small class="timeStamp"><?php assign(date("H:i - Y年n月j日", strtotime($panel['entry']['pub_date']))); ?></small>
                    <!-- /.contText --></p>
                <!-- /.twInner --></a>
            <ul class="twActions">
                <li><a href="//twitter.com/intent/follow?screen_name=<?php assign($panel['screenName'])?>" class="twFollow">フォローする</a></li>
                <li><a href="//twitter.com/intent/tweet?in_reply_to=<?php assign($panel['entry']["object_id"])?>" class="twReply" target="_blank">リプライ</a></li>
                <li><a href="//twitter.com/intent/retweet?tweet_id=<?php assign($panel['entry']["object_id"])?>" class="twRetweet">リツイート</a></li>
                <li><a href="//twitter.com/intent/favorite?tweet_id=<?php assign($panel['entry']["object_id"])?>" class="twFavo">お気に入り</a></li>
                <!-- /.twActions --></ul>
            <!-- /.snsPageList --></section>
    <?php elseif ($panel['streamName'] == 'FacebookStream'): ?>
        <section class="snsPageList jsPanel">
            <a href="<?php assign($panel['entry']['page_link']); ?>" class="fbInner panelClick"
               data-link="<?php assign($panel['entry']['link']); ?>"
               data-entry="<?php assign(StreamService::STREAM_TYPE_FACEBOOK)?>"
               data-type="<?php assign(UserPanelClick::CATEGORY_PANEL); ?>">
                <p class="contText">
                    <?php if ($panel['entry']['image_url']): ?>
                        <img src="<?php assign($panel['entry']['image_url']); ?>" alt="">
                    <?php endif; ?>

                    <?php if ($panel['entry']['panel_text']): ?>
                        <span><?php assign($panel['entry']['panel_text']); ?></span>
                    <?php endif; ?>
                    <!-- /.contText --></p>
                <!-- /.fbInner --></a>
            <!-- /.snsPageList --></section>
    <?php elseif ($panel['streamName'] == 'YoutubeStream'): ?>
        <section class="snsPageList jsPanel">
            <a href="<?php assign($panel['entry']['page_link']); ?>" class="ytInner panelClick"
               data-link="<?php assign($panel['entry']['link']); ?>"
               data-entry="<?php assign(StreamService::STREAM_TYPE_YOUTUBE)?>"
               data-type="<?php assign(UserPanelClick::CATEGORY_PANEL); ?>">
                <p class="contText">
                    <span class="captcha"><img src="<?php assign($panel['entry']['image_url']); ?>" alt=""></span>
                    <span><?php assign($panel['entry']['panel_text']); ?></span>
                    <!-- /.contText --></p>
                <!-- /.ytInner --></a>
            <!-- /.snsPageList --></section>
    <?php elseif ($panel['streamName'] == 'InstagramStream'): ?>
        <section class="snsPageList jsPanel">
            <a href="#instagram_modal" class="igInner panelClick jsOpenIGModal"
               data-entry_id="<?php assign($panel['entry']['id']); ?>"
               data-link="<?php assign($panel['entry']['link']); ?>"
               data-entry="<?php assign(StreamService::STREAM_TYPE_INSTAGRAM); ?>"
               data-type="<?php assign(UserPanelClick::CATEGORY_PANEL); ?>">
                <p class="contImg">
                    <img src="<?php assign($panel['entry']['image_url']); ?>" alt="">
                    <!-- /.contImg --></p>
                <!-- /.igInner --></a>
            <!-- /.snsPageList --></section>
    <?php endif; ?>
<?php endforeach; ?>