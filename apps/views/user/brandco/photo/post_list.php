<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

<article class="mainCol">

    <div class="photoPageWrap">
        <header class="photoPageHeader">
            <h1><?php assign($data['page_data']['page_title']) ?></h1>
            <!-- /.photoPageHeader --></header>

        <?php if ($data['page_data']['photo_entries']->total() > 0): ?>
            <ul class="photoPageList" id="jsPhotoInfiniteScroll">
                <?php $li_count = 0; ?>
                <?php foreach($data['page_data']['photo_entries'] as $photo_entry): ?>
                    <?php if ($li_count != 0) write_html('-->'); ?><li class="jsPhotoPanel">
                    <?php $photo_user = $photo_entry->getPhotoUser(); ?>
                    <a href="<?php assign(Util::rewriteUrl('photo', 'detail', array($photo_entry->id))); ?>">
                        <img src="<?php assign($photo_user->getCroppedPhoto()); ?>" alt="<?php assign($photo_user->photo_title); ?>" onerror="this.src='<?php assign($photo_user->photo_url); ?>';">
                    </a>
                    </li><?php if (++$li_count != $data['page_data']['photo_entries']->total()) write_html('<!--'); ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Load more parameter -->
        <?php if ($data['page_data']['load_more_flg']): ?>
            <p class="jsPanel loading" id="more_page_loading" style="display: none">
                <img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif')); ?>" alt="loading">
                <!-- /.loading --></p>
            <p class="jsPanel morePanels" id="more_page_btn">
                <span class="btn2"><a href="javascript:void(0)" class="small1" id="more_panel">more</a></span>
            </p>
            <?php write_html($this->formHidden('more_page_url', Util::rewriteUrl('photo', 'get_panel_for_page', null, array('cp_action_id' => $data['page_data']['cp_action_id'], 'p' => '')))); ?>
            <?php write_html($this->formHidden('sp_panel_per_page', $data['page_data']['sp_panel_per_page'])); ?>
            <?php write_html($this->formHidden('total_count', $data['page_data']['total_count'])); ?>
        <?php endif; ?>

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
        <!-- /.photoPageWrap --></div>

    <?php if ($this->brand_contract->plan == BrandContract::PLAN_MANAGER_STANDARD && $this->brand->hasOption(BrandOptions::OPTION_TOP)): ?>
        <p class="pagePrev"><a href="<?php Util::rewriteUrl('', ''); ?>">一覧へ戻る</a></p>
    <?php endif; ?>

    <?php if ($this->brand_contract->plan == BrandContract::PLAN_MANAGER_STANDARD && $this->brand->hasOption(BrandOptions::OPTION_TOP)): ?>
        <nav class="bredlink1">
            <ul>
                <li class="home"><a href="<?php assign(Util::rewriteUrl('', '')); ?>">HOME</a></li>
                <li class="current"><span><?php assign($data['page_data']['page_title']) ?></span></li>
            </ul>
            <!-- /.bredlink1 --></nav>
    <?php endif; ?>
    <p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
</article>

<a id="nextAnchor" href="#" style="visibility:hidden">next</a>
<?php if (!Util::isSmartPhone()): ?>
    <script src="<?php assign($this->setVersion('/js/infinitescroll/jquery.infinitescroll.js')) ?>"></script>

    <script type="text/javascript">
        $('#jsPhotoInfiniteScroll').infinitescroll({
            navSelector: "#nextAnchor:last",
            nextSelector: "a#nextAnchor:last",
            itemSelector: "li.jsPhotoPanel",
            debug: false,
            dataType: 'html',
            maxPage: undefined,
            loading: {
                msg: $('<div></div>')
            },
            path: function (index) {
                return "<?php write_html(Util::rewriteUrl('photo', 'get_panel_for_page', null, array('cp_action_id' => $data['page_data']['cp_action_id'], 'p' => ''))); ?>" + index;
            }
        });

    </script>
<?php endif; ?>

<?php
    $script = array('BrandcoPhotoPageService');
    $param = array_merge($data['pageStatus'], array('script' => $script));
?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
