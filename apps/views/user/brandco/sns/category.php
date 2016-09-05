<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

<?php $page_name = $this->brand_social_account->social_app_id == SocialApps::PROVIDER_TWITTER ? $this->brand_social_account->name . '@' . $this->brand_social_account->screen_name : $this->brand_social_account->name; ?>

<article class="mainCol">
    <?php write_html($this->csrf_tag()); ?>

    <?php write_html($this->parseTemplate('SnsPageAccountInfo.php', $this->brand_social_account)); ?>

    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoSnsPage')->render(array_merge($data['pageStatus'], array('brand_social_account_id' => $this->brand_social_account->id)))) /*メインカラム*/ ?>

    <p class="pagePrev"><a href="<?php assign(Util::rewriteUrl('', '')); ?>">一覧へ戻る</a></p>

    <nav class="bredlink1">
        <ul>
            <li class="home"><a href="<?php assign(Util::rewriteUrl('', '')); ?>">HOME</a></li>
            <li class="current"><span><?php assign('[' . $this->page_data['sns_name'] . '] ' . $page_name); ?></span></li>
        </ul>
        <!-- /.bredlink1 --></nav>
    <p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>

    <!-- /.mainCol --></article>

<a id="nextAnchor" href="#" style="visibility:hidden">next</a>
<?php if (!Util::isSmartPhone()): ?>
    <script src="<?php assign($this->setVersion('/js/infinitescroll/jquery.infinitescroll.js')) ?>"></script>

    <script type="text/javascript">
        $('#jsSnsInfiniteScroll').infinitescroll({
            navSelector: "#nextAnchor:last",
            nextSelector: "a#nextAnchor:last",
            itemSelector: "section.jsPanel",
            debug: false,
            dataType: 'html',
            maxPage: undefined,
            loading: {
                msg: $('<div></div>')
            },
            path: function (index) {
                return "<?php write_html(Util::rewriteUrl('sns', 'get_panel_for_page',null,array('preview' => $params['preview'], 'b' => $this->brand_social_account->id, 'p' => ''))); ?>" + index;
            }
        }, function (newElements, data, url) {
            if (!Brandco.unit.isSmartPhone) {
                BrandcoMasonryCategoryService.sortPanel($(this), true);
            }
        });

    </script>
<?php endif; ?>

<?php
    $script = array('BrandcoSnsPageService', 'BrandcoInstagramService');
    $param = array_merge($data['pageStatus'], array('script' => $script));
?>
<?php write_html($this->parseTemplate('BrandcoInstagramModal.php')); ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
