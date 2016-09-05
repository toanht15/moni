<div class="snsPageWrap jsMasonry" id="sortable">
    <div id="jsSnsInfiniteScroll">

        <?php foreach($data['panel_list'] as $panel): ?>

            <?php if ($panel['streamName'] == 'TwitterStream'): ?>
                <section class="snsPageList jsPanel">
                    <a href="<?php assign($panel['entry']['page_link']); ?>" class="twInner panelClick"
                       data-link="<?php assign($panel['entry']['link']); ?>"
                       data-entry="<?php assign(StreamService::STREAM_TYPE_TWITTER)?>"
                       data-type="<?php assign(UserPanelClick::CATEGORY_PANEL); ?>"
                       data-entry_id="<?php assign($panel['entry']['id']) ?>">
                        <p class="contText">
                            <?php write_html($this->nl2brAndHtmlspecialchars($panel['entry']['panel_text'])); ?>

                            <?php if ($panel['entry']['image_url']): ?>
                                <img src="<?php assign($panel['entry']['image_url']); ?>" width="285" height="241" alt="">
                            <?php endif; ?>

                            <small class="timeStamp"><?php assign(date("H:i - Y年n月j日", strtotime($panel['entry']['pub_date']))); ?>
                                <span class="timeLogo"><span class="iconTW2_2">Twitter</span></span>
                            </small>
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
                           data-type="<?php assign(UserPanelClick::CATEGORY_PANEL); ?>"
                           data-entry_id="<?php assign($panel['entry']['id']) ?>">
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
                           data-type="<?php assign(UserPanelClick::CATEGORY_PANEL); ?>"
                           data-entry_id="<?php assign($panel['entry']['id']) ?>">
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
                           data-type="<?php assign(UserPanelClick::CATEGORY_PANEL); ?>"
                           data-entry_id="<?php assign($panel['entry']['id']) ?>">
                            <p class="contImg">
                                <img src="<?php assign($panel['entry']['image_url']); ?>" alt="">
                                <!-- /.contImg --></p>
                            <!-- /.igInner --></a>
                        <!-- /.snsPageList --></section>
                <?php endif; ?>
        <?php endforeach; ?>

        <!-- Load more parameter -->
        <?php if ($data['load_more_flg']): ?>
            <p class="jsPanel loading" id="more_page_loading" style="display: none">
                <img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif')); ?>" alt="loading">
                <!-- /.loading --></p>
            <p class="jsPanel morePanels" id="more_page_btn">
                <span class="btn2"><a href="javascript:void(0)" class="small1" id="more_panel">more</a></span>
            </p>
            <?php write_html($this->formHidden('more_page_url', Util::rewriteUrl('sns', 'get_panel_for_page',null,array('preview' => $data['preview'], 'b' => $data['brand_social_account_id'], 'p' => '')))); ?>
            <?php write_html($this->formHidden('sp_panel_per_page', $data['sp_panel_per_page'])); ?>
            <?php write_html($this->formHidden('total_count', $data['total_count'])); ?>
        <?php endif; ?>

        <!-- /.jsSnsInfiniteScroll --></div>

    <section class="snsWrap jsPanel">
        <ul class="snsBtns-btn">
            <li><div class="fb-like" data-href="<?php assign(Util::getCurrentUrl()); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div></li
                ><li><a href="<?php assign(Util::getCurrentUrl()); ?>" class="twitter-share-button" data-lang="ja" data-count="none">ツイート</a></li
                ><li><div class="g-plusone" data-size="medium" data-annotation="none"></div></li
                >
            <!-- /.snsBtns-btn --></ul>
        <ul class="snsBtns-box">
            <li><div class="fb-like" data-href="<?php assign(Util::getCurrentUrl()); ?>" data-layout="box_count" data-action="like" data-show-faces="false" data-share="false"></div></li
                ><li><a href="<?php assign(Util::getCurrentUrl()); ?>" class="twitter-share-button" data-lang="ja" data-count="vertical">ツイート</a></li
                ><li><div class="g-plusone" data-size="tall"></div></li
                ><li class="line"><span><script type="text/javascript" src="//media.line.me/js/line-button.js?v=20140411" ></script><script type="text/javascript">new media_line_me.LineButton({"pc":false,"lang":"ja","type":"e"});</script></span></li>
            <!-- /.snsBtns-box --></ul>
        <!-- /.snsWrap --></section>

    <!-- /.snsPageWrap --></div>
