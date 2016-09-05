<section class="snsPageYt">
    <div class="movie"><div class="inner"><iframe src="<?php assign('https://www.youtube.com/embed/' . $data['sns_entry']->object_id . '?rel=0&showinfo=0'); ?>" frameborder="0" allowfullscreen></iframe></div></div>

    <div class="postWrap">
        <p class="postText">
            <?php write_html($this->toHalfContentDeeply(json_decode($data['sns_entry']->extra_data)->snippet->description))?>
            <small class="timeStamp"><?php assign(date("Y/m/d", strtotime($data['sns_entry']->pub_date))); ?> に公開</small>
        </p>

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
    <!-- /.snsPageYt --></section>