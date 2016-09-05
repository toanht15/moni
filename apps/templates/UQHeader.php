<header>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeaderAccountSection')->render($data)) ?>

    <div id="uqwimaxHeader">
        <div id="uqwimaxHeaderInner">
            <h1><a href="<?php assign(Util::rewriteUrl('', '')) ?>"><img src="<?php assign($this->setVersion('/brand/fan.uqwimax.jp/img/base/logo.gif')) ?>" alt="みんなでつながる UQひろば" width="194"></a></h1>
            <p class="uqwimaxIcon"><img src="<?php assign($this->setVersion('/brand/fan.uqwimax.jp/img/base/iconUQ.gif')) ?>" alt="UQ LOVES YOU" width="31"></p>
            <nav>
                <div class="uqwimaxHeaderMenuHeader">
                    <p class="logoMenu"><a href="<?php assign(Util::rewriteUrl('', '')) ?>"><img src="<?php assign($this->setVersion('/brand/fan.uqwimax.jp/img/base/logoMenu.png')) ?>" alt="みんなでつながる UQひろば" width="226"></a></p>
                    <button id="uqwimaxHeaderMenuBtn"></button>
                <!-- /.uqwimaxHeaderMenuHeader --></div>
                <ul>
                    <li class="uqwimaxHeaderMenuTshushin"><a href="<?php assign(Util::rewriteUrl('categories', 'tsushin')) ?>">UQ通信</a></li>
                    <li class="uqwimaxHeaderMenuKuchikomi"><a href="<?php assign(Util::rewriteUrl('page', 'review')) ?>">みんなのクチコミ</a></li>
                    <li class="uqwimaxHeaderMenuMatome"><a href="<?php assign(Util::rewriteUrl('page', 'sns')) ?>">SNSまとめ</a></li></a></li>
                    <li class="uqwimaxHeaderMenuClose"><a href="#" id="uqwimaxHeaderMenuCloseBtn"><img src="<?php assign($this->setVersion('/brand/fan.uqwimax.jp/img/base/btnMenuClose.png')) ?>" width="160" alt="閉じる"></a></li></a></li>
                </ul>
            </nav>
            <ul class="uqwimaxHeaderSnsBtns-btn">
                <li><div class="fb-like" data-href="https://developers.facebook.com/docs/plugins/" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div></li><li><a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-count="none">ツイート</a></li><li><a href="<?php assign(Util::rewriteUrl('page', 'beginner')) ?>"><img src="<?php assign($this->setVersion('/brand/fan.uqwimax.jp/img/base/btnVisitor.gif')) ?>" width="82" alt="初めての方へ"></a></li>
            <!-- /.snsBtns-btn --></ul>
        <!-- /#uqwimaxHeaderInner --></div>
    <!-- /#uqwimaxHeader --></div>
</header>