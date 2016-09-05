<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>
<article>
    <?php write_html($this->csrf_tag()); ?>
    <script src="<?php assign($this->setVersion('/js/infinitescroll/jquery.infinitescroll.js')) ?>"></script>

    <?php if ($data['isDisplayFreeArea']): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopFreeArea')->render($data['pageStatus'])) /*フリーエリア*/ ?>
    <?php endif; ?>

    <?php if ($data['pageStatus']['isLoginAdmin'] && !$data['pageStatus']['isAgent']): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopAddPanel')->render(array(
            'brand' => $data['pageStatus']['brand'],
            'backFromSNSConnect' => ($data['pageStatus']['isLoginAdmin']) ? $data['ActionForm']['connect'] : false,
        )))/*管理モード*/
        ?>
    <?php endif; ?>

    <article class="mainCol jsMasonry" id="sortable">
        <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopMainCol')->render($data['pageStatus'])) /*メインカラム*/ ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopSideCol')->render($data['pageStatus'])) /*サイドカラム*/ ?>
        <!-- /.mainCol --></article>
    <p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
<!-- /.wrap --></article>

<a id="nextAnchor" href="#" style="visibility:hidden">next</a>
<?php if (!Util::isSmartPhone()): ?>
    <script type="text/javascript">
        $('#jsNormalSortable').infinitescroll({
            navSelector: "#nextAnchor:last",
            nextSelector: "a#nextAnchor:last",
            itemSelector: "section.jsPanel",
            debug: false,
            dataType: 'html',
            maxPage: undefined,
            loading: {
                finishedMsg: '新しいコンテンツがありません.',
                msgText: "<em>コンテンツをロード中...</em>",
                img: '<?php assign($this->setVersion('/js/infinitescroll/ajax-loader.gif'))?>'
            },
            path: function (index) {
                return "<?php write_html(Util::rewriteUrl('admin-top', 'get_panel_for_page',null,array('preview' => $params['preview'],'p' => '')))?>" + index;
            }
        }, function (newElements, data, url) {
            if (!Brandco.unit.isSmartPhone) {
                BrandcoMasonryTopService.sortPanel($(this), true);
            }
            <?php if($data['pageStatus']['isLoginAdmin']):?>
            BrandcoTopMainColService.init();
            <?php endif;?>
        });

    </script>
<?php endif; ?>

<?php if ($data['pageStatus']['isLoginAdmin'] && !$data['pageStatus']['isAgent']): ?>

    <div class="modal1 jsModal" id="editProfile">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'edit_profile_form')) ?>"
                    frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="globalMenus">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'global_menus')) ?>" frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="selectPanelKind">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'select_panel_kind')) ?>"
                    frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="connectFBPanelKind">
        <section class="modalCont-large jsModalCont">
            <iframe
                data-src="<?php assign(Util::rewriteUrl('facebook', 'connect', array(), array('code' => $_GET['code'], 'state' => $_GET['state'], 'error_reason' => $_GET['error_reason']))) ?>"
                frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="editFBPanelForm">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'edit_facebook_panel_form')) ?>"
                    frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="editTWPanelForm">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'edit_twitter_panel_form')) ?>"
                    frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="editLIPanelForm">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'edit_link_entry_form')) ?>"
                    frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="editYTPanelForm">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'edit_youtube_panel_form')) ?>"
                    frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="editRSSPanelForm">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'edit_rss_entry_form')) ?>"
                    frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="editIGPanelForm">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'edit_instagram_panel_form')) ?>"
                    frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="editPHPanelForm">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'edit_photo_entry_form')) ?>"
                    frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="editPGPanelForm">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'edit_page_entry_form')) ?>"
                    frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="sideMenus">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'side_menus')) ?>" frameborder="0"></iframe>
        </section>
    </div>

    <div class="modal1 jsModal" id="freeAreaEntries">
        <section class="modalCont-large jsModalCont">
            <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'free_area_entries')) ?>"
                    frameborder="0"></iframe>
        </section>
    </div>

<?php endif; ?>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<?php $script = array('BrandcoInstagramService');
if ($data['pageStatus']['isLoginAdmin'] && !$data['pageStatus']['isAgent']) {
    $script[] = 'BrandcoTopMainColService';
    $script[] = 'BrandcoTopSideColService';
}
if (Util::isSmartPhone()) {
    $script[] = 'BrandcoTopMainColUserSPService';
} else {
    $script[] = 'BrandcoTopMainColUserService';
}
?>

<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>
<?php write_html($this->parseTemplate('BrandcoInstagramModal.php')); ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
