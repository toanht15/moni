<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

<?php $service_factory = new aafwServiceFactory();
/** @var StaticHtmlCategoryService $static_html_category_service */
$static_html_category_service = $service_factory->create('StaticHtmlCategoryService');
?>
<article>
    <article class="mainCol">
        <div class="mainContWrap">
            <section class="pageWrap">
                <header>
                    <h1><?php assign($data['current_category']['name']) ?></h1>
                    <?php if (!Util::isSmartPhone() && count($data['sns_plugin_ids'])): ?>
                        <?php write_html($this->parseTemplate('PageContentHeader.php', array(
                            'custom_plugin' => $data['current_category']['sns_plugin_tag_text'],
                            'sns_plugin_ids' => $data['sns_plugin_ids']
                        ))); ?>
                    <?php endif; ?>
                </header>
                <ul class="pageList" id="jsCategoryInfiniteScroll">
                    <?php foreach ($data['static_entries'] as $static_entry): ?>
                        <li class="jsCategoryPanel">
                            <a href="<?php write_html(Util::rewriteUrl('', 'page', array($static_entry->page_url))) ?>">
                                <figure class="pageImg"><img src="<?php assign($static_entry->getImageUrl($data['brand'])) ?>" alt="<?php assign($static_entry->title) ?>"></figure>
                                <p class="pageData">
                                    <strong><?php assign($static_entry->title) ?></strong>
                                    <span class="description"><?php
                                        if($static_entry->meta_description) {
                                            assign($this->cutLongText($static_entry->meta_description));
                                        }else if($static_entry->write_type == StaticHtmlEntries::WRITE_TYPE_BLOG) {
                                            assign($this->cutLongText(str_replace('&nbsp;', '', $static_entry->body, $static_entry->body), 150));
                                        }?></span>
                                    <?php if (!$data['hidden_date_flg']): ?>
                                        <span class="date"><?php assign(date('Y/m/d', strtotime($static_entry->public_date))) ?></span>
                                    <?php endif; ?>
                                </p>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <!-- /.pageList --></ul>

                <?php if (Util::isSmartPhone() && $data['total_page'] > 1): ?>
                    <p class="loading" id="jsMorePageLoading" style="display: none">
                        <img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif')); ?>" alt="loading">
                        <!-- /.loading --></p>
                    <p class="morePanels">
                        <span class="btn3"><a href="javascript:void(0)" class="small1 jsMoreLoad">more</a></span>
                    <!-- /.morePanels --></p>
                    <?php write_html($this->formHidden('more_page_url', Util::rewriteUrl('blog', 'get_static_html_entries_for_page', null, array('category_directory' => $data['current_category']['directory'],'p' => '')))); ?>
                    <?php write_html($this->formHidden('total_page', $data['total_page'])); ?>
                <?php endif; ?>

                <?php if (Util::isSmartPhone() && count($data['sns_plugin_ids'])): ?>
                    <?php write_html($this->parseTemplate('PageContentHeaderSP.php', array('sns_plugin_ids' => $data['sns_plugin_ids'], 'custom_plugin' => $data['staticHtmlEntry']['sns_plugin_tag_text']))); ?>
                <?php endif; ?>
                <!-- /.pageWrap--></section>
            <?php write_html($this->parseTemplate('PageNavigation.php', array('current_category_name' => $data['current_category']['name'],
                    'father_category' => $data['father_category'],
                    'grandfather_category' => $data['grandfather_category']))); ?>
        <!-- /.mainContWrap--></div>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('PageTopSideCol')->render($data['pageStatus'])) /*サイドカラム*/ ?>

    <!-- /.mainCol --></article>
<!-- /.wrap --></article>

<script src="<?php assign($this->setVersion('/js/infinitescroll/jquery.infinitescroll.min.js')) ?>"></script>
<?php if (!$data['current_category']['is_use_customize'] && !Util::isSmartPhone()): ?>
    <a id="nextAnchor" href="javascript:void(0)" style="visibility:hidden">next</a>
<?php endif; ?>

<style type="text/css">
    #infscr-loading {
        text-align:center;
        margin: 0 auto;
    }
    #infscr-loading img {
        width: <?php assign(Util::isSmartPhone() ? '25px;' : '40px') ?>
    }
</style>
<script type="text/javascript">
    $('#jsCategoryInfiniteScroll').infinitescroll({
        navSelector: "#nextAnchor:last",
        nextSelector: "a#nextAnchor:last",
        itemSelector: "li.jsCategoryPanel",
        debug: false,
        dataType: 'html',
        maxPage: <?php assign($data['total_page']) ?>,
        loading: {
            msg: '',
            finishedMsg: '',
            msgText: '',
            img: '/img/mail/common/amimeLoading.gif'
        },
        path: function (index) {
            return "<?php write_html(Util::rewriteUrl('blog', 'get_static_html_entries_for_page', null, array('category_directory' => $data['current_category']['directory'],'p' => ''))); ?>" + index;
        }
    });

    <?php if (!$data['current_category']['is_use_customize'] && Util::isSmartPhone()): ?>
        $('#jsCategoryInfiniteScroll').infinitescroll('unbind');
        $('#nextAnchor').on('click', function(){
            $('#jsCategoryInfiniteScroll').infinitescroll('retrieve');
            return false;
        });
    <?php endif; ?>
</script>

<?php
$script = array('admin-blog/CmsCategoryService', 'PageService');
$param = array_merge($data['pageStatus'], array('script' => $script));
write_html($this->parseTemplate('BrandcoFooter.php', $param));
?>
