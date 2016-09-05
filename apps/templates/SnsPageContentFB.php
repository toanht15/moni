<section class="snsPageFb">
    <?php if ($data['sns_entry']->type == FacebookEntry::ENTRY_TYPE_VIDEO): ?>
        <div class="movie">
            <?php if ($data['sns_entry']->getStatusType() == FacebookEntry::STATUS_TYPE_ADDED_VIDEO): ?>
                <div class="fb-video" data-href="<?php assign($data['sns_entry']->getVideoSource()) ?>" data-allowfullscreen="true"></div>
            <?php else: ?>
                <div class="inner"><iframe src="<?php assign($data['sns_entry']->getVideoSource()) ?>" frameborder="0" allowfullscreen></iframe></div>
            <?php endif; ?>
        </div>
    <?php elseif ($data['sns_entry']->image_url): ?>
        <figure><img src="<?php assign($data['sns_entry']->image_url); ?>" alt=""></figure>
    <?php endif; ?>

    <div class="postWrap">
        <p class="postText">
            <?php write_html(SNSParser::parseText(json_decode($data['sns_entry']->extra_data)->message, SNSParser::FACEBOOK_PANEL_TEXT))?>
        </p>

        <p class="postLisk"><a href="<?php assign($data['sns_entry']->link)?>" <?php if ($data['sns_entry']->target_type == FacebookEntry::TARGET_TYPE_BLANK): ?>target="_blank"<?php endif; ?>>
                <span class="iconFB2">facebook</span><?php assign($data['brand_social_account']->name); ?>　<?php assign(date("Y/m/d", strtotime($data['sns_entry']->pub_date))); ?>
            </a></p>

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
        <!-- /.postWrap --></div>
    <!-- /.snsPageFb --></section>
