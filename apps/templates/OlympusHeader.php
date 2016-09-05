<header>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeaderAccountSection')->render($data)) ?>
    <?php if (Util::isSmartPhone()): ?>
        <p class="spFanNumber"><?php assign(number_format($data['brand_info']['users_num'])); ?>人</p>
    <?php endif; ?>
</header>
<!-- olympus header  -->
<div id="olympusHeader"><div id="olympusHeaderInner">
        <div id="olympusHeaderContent">
            <p id="logoOlympus">
                <img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/olympus-imaging.jp/000000002.svg')) ?>" alt="OLYMPUS" class="svg" />
                <img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/olympus-imaging.jp/000000003.svg')) ?>" alt="" class="svgSp" />
                <img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/olympus-imaging.jp/000000004.gif')) ?>" alt="" class="image" />
                <!-- /#logoOlympus --></p>
            <div id="logoOlympusSub">
                <p><a href="http://www.olympus.co.jp/">Olympus Japan</a></p>
                <p><a href="http://www.olympus-global.com/en/network/"><img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/olympus-imaging.jp/000000017.gif')) ?>" alt="OLYMPUS" /></a></p>
                <!-- /#logoOlympusSub --></div>
            <p id="logoOlympusImaging">
                <a href="http://olympus-imaging.jp/">
                    <img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/olympus-imaging.jp/000000005.svg')) ?>" alt="OLYMPUS IMAGING" class="svg" />
                    <img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/olympus-imaging.jp/000000006.gif')) ?>" alt="OLYMPUS IMAGING" class="image" />
                </a>
            </p>
            <!-- /#olympusHeaderContent --></div>
        <!-- /#olympusHeaderInner --></div>

    <div id="olympusVoiceRecorderHeader">
        <h1><a href="https://voicerecorder.olympus-imaging.com/"><img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/common/logo.gif')) ?>" width="223" alt="使える! ボイスレコーダー"></a></h1>
        <button id="btnOlympusMenu"><img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/common/btnMenu.gif')) ?>" width="45"></button>
        <nav id="olympusGlobalNav">
            <ul>
                <li><a href="<?php assign(Util::rewriteUrl('', '')) ?>" class="nav1"><img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/common/btnGlobalNav1.gif')) ?>" alt="TOP"></a></li>
                <li><a href="<?php assign(Util::rewriteUrl('page', 'introduce')) ?>" class="nav2"><img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/common/btnGlobalNav2.gif')) ?>" alt="なぜレコーダーが選ばれるの？"></a></li>
                <li><a href="<?php assign(Util::rewriteUrl('categories', 'use')) ?>" class="nav3"><img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/common/btnGlobalNav3.gif')) ?>" alt="ボイスレコーダー活用術"></a></li>
                <li><a href="<?php assign(Util::rewriteUrl('page', 'type')) ?>" class="nav4"><img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/common/btnGlobalNav4.gif')) ?>" alt="レコーダーのタイプ"></a></li>
                <li><a href="<?php assign(Util::rewriteUrl('page', 'original')) ?>" class="nav5"><img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/common/btnGlobalNav5.gif')) ?>" alt="オリンパスのボイスレコーダーはここがすごい！"></a></li>
                <li><a href="<?php assign(Util::rewriteUrl('page', 'navigation')) ?>" class="nav6"><img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/common/btnGlobalNav6.gif')) ?>" alt="どれ買うナビ"></a></li>
                <li><a href="<?php assign(Util::rewriteUrl('categories', 'special')) ?>" class="nav7"><img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/common/btnGlobalNav7.gif')) ?>" alt="スペシャルコンテンツ"></a></li>
                <li><a href="#" id="olympusGlobalNavClose"><img src="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/img/common/btnGlobalNav8.gif')) ?>" alt="閉じる"></a></li>
            </ul>
        </nav>
        <!-- /#olympusVoiceRecorderHeader --></div>
    <!-- /#olympusHeader --></div>
<!-- / olympus header  -->