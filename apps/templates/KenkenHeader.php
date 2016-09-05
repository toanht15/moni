<header>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeaderAccountSection')->render($data)) ?>

    <div id="kenkenHeader">
        <div id="kenkenHeaderInner">
            <h1><a href="<?php assign(Util::rewriteUrl('', '')) ?>"><img src="<?php assign($this->setVersion('/brand/kenken.or.jp/img/base/logo.gif')) ?>" alt="健検 日本健康マスター検定" width="75"><span>あなたの”健康リテラシー”、大丈夫？</span></a></h1>
            <button id="kenkenHeaderMenuBtn"></button>
            <nav>
                <ul>
                    <li><a href="<?php assign(Util::rewriteUrl('', '')) ?>">トップ</a></li>
                    <li><a href="<?php assign(Util::rewriteUrl('page', 'about')) ?>">協会概要</a></li>
                    <li><a href="<?php assign(Util::rewriteUrl('r', 'column')) ?>">コラム</a></li>
                    <li><a href="<?php assign(Util::rewriteUrl('page', 'submit')) ?>">試験概要・申し込み</a></li>
                    <li><a href="<?php assign(Util::rewriteUrl('page', 'text')) ?>">公式テキスト</a></li>
                </ul>
            </nav>
            <!-- /#kenkenHeaderInner --></div>
        <!-- /#kenkenHeader --></div>
</header>
