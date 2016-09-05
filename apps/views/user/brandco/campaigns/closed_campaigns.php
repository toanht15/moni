<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

<article>
    <section class="messageWrap">
        <section class="message_end">
            <section class="messageText">
                <p class="attention1"><strong><?php assign($data['cp']->getTitle());?></strong>は終了しました。</p>
            </section>

                <?php if ($data['brand']->getBrandContract()->plan == BrandContract::PLAN_MANAGER_STANDARD && $data['brand']->hasOption(BrandOptions::OPTION_TOP)): ?>
                    <div class="engagementInner">
                        <div class="aboutAccount">
                            <figure class="accountIcon"><img src="<?php assign($data['brand']->getProfileImage());?>" alt="heinzketchup_jp" width="100" height="100"></figure>
                            <p class="accountName">
                                <strong><?php assign($data['brand']->name);?></strong>
                                <span><?php assign($data['brand']->getBrandPageSetting()->meta_description);?></span>
                            </p>
                        <!-- /.aboutIgAccount --></div>
                        <p class="buttonCaption">こちらのブランドに興味を持ったら</p>
                        <ul class="btnSet">
                            <li class="btn3"><a href="<?php assign($data['brand']->getUrl());?>" class="large2">ブランドサイトに行く</a></li>
                        </ul>
                    <!-- /.engagementInner --></div>
                <?php else: ?>
                    <div class="cpPrev">
                        <figure><img src="<?php assign($this->setVersion('/img/message/monipla_banner.jpg'))?>" alt=""></figure>
                        <p>
                            <strong class="title">モニプラではたくさんのキャンペーンが開催中です！</strong>
                            <small class="description">食品、人気の豪華家電、ギフト券などが当たるキャンペーン情報満載！あなたが欲しいプレゼントもあるかも？</small>
                        </p>
                        <ul class="btnSet">
                            <li class="btn4"><a href="<?php assign('https://'.config('Domain.monipla_media').'/login?ref=cmcpth'); ?>" class="large2">他のキャンペーンを探す</a></li>
                            <!-- /.btnSet --></ul>
                        <!-- /.cpPrev --></div>
                <?php endif; ?>

            <div class="messageFooter">
                <p class="date"></p>
                <!-- /.messageFooter --></div>
            <!-- /.campaignEnd --></section>
        <!-- /.messageWrap --></section>
</article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
