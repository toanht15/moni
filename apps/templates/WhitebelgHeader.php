<!-- whitebelg header  -->
<header>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeaderAccountSection')->render($data)) ?>
</header>
<ul class="snsBtns-btn whitebelgHeadSnsBtns-btn">
    <li><div class="fb-like" data-href="<?php assign(Util::getCurrentUrl()) ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div></li>
    <li><a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-count="none">ツイート</a></li>
    <!-- /.snsBtns-btn --></ul>
<div id="whitebelgHeader" class="whitebelgHeaderHiddenFanNum"> <div id="whitebelgHeaderInner">
    <h1><a href="<?php assign(Util::rewriteUrl('', '')) ?>"><img src="<?php assign($this->setVersion('/brand/whitebelg/img/base/logo.png')) ?>" width="252" alt="ようこそ！ホワイトベルグゼミへ！ WHITE BELG SEMINAR"></a></h1>
    <nav>
        <a href="#" id="whitebelgBtnToggleNav">閉じる</a>
        <ul>
            <li class="whitebelgNavSeminar"><a href="<?php assign(Util::rewriteUrl('page', 'about')) ?>">ホワイトベルグゼミとは？</a></li>
            <li class="whitebelgNavScholarship"><a href="<?php assign(Util::rewriteUrl('r', 'scholarship')) ?>">ホワイトベルグ奨学金！</a></li>
            <li class="whitebelgNavTime"><a href="<?php assign(Util::rewriteUrl('', '').'#whitebelgTopPageTheme') ?>">課題の時間！</a></li>
            <li class="whitebelgNavSpecialLecture"><a href="<?php assign(Util::rewriteUrl('', '').'#whitebelgTopPageSpecialLecture') ?>">ホワイトベルガーによる特別講義</a></li>
            <li class="whitebelgNavStudyGroup"><a href="<?php assign(Util::rewriteUrl('page', 'society')) ?>">ベルグ研究会</a></li>
            <li class="whitebelgNavClose"><a href="#">× 閉じる</a></li>

        </ul>
    </nav>
    <!-- /#whitebelgHeader --></div> </div>