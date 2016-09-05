<section class="snsPageTw">
    <p class="contText">
        <?php write_html(SNSParser::parseText(json_decode($data['sns_entry']->extra_data)->text, SNSParser::TWITTER_PANEL_TEXT))?>
        <?php if ($data['sns_entry']->image_url): ?>
            <img src="<?php assign($data['sns_entry']->image_url); ?>" width="285" height="241" alt="">
        <?php endif; ?>
        <small class="timeStamp"><?php assign(date("H:i - Y年n月j日", strtotime($data['sns_entry']->pub_date))); ?>
            <span class="timeLogo"><span class="iconTW2_2">Twitter</span></span>
        </small>
        <!-- /.contText --></p>
    <ul class="twActions">
        <li><a href="//twitter.com/intent/follow?screen_name=<?php assign(json_decode($data['sns_entry']->extra_data)->user->screen_name)?>" class="twFollow"><span>フォローする</span></a></li>
        <li><a href="//twitter.com/intent/tweet?in_reply_to=<?php assign($data['sns_entry']->object_id)?>" class="twReply"><span>リプライ</span></a></li>
        <li><a href="//twitter.com/intent/retweet?tweet_id=<?php assign($data['sns_entry']->object_id)?>" class="twRetweet"><span>リツイート</span></a></li>
        <li><a href="//twitter.com/intent/favorite?tweet_id=<?php assign($data['sns_entry']->object_id)?>" class="twFavo"><span>お気に入り</span></a></li>
        <!-- /.twActions --></ul>

    <div class="snsWrap">
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
        <!-- /.snsWrap --></div>
    <!-- /.snsPageTw --></section>

