<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php
$data['pageStatus']['layout_type'] += $data['staticHtmlEntry']['layout_type'];
write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus']))
?>

<article class="mainCol" >
    <?php if ($data['staticHtmlEntry']['layout_type'] == StaticHtmlEntries::LAYOUT_NORMAL): ?>
        <div class="mainContWrap">
    <?php endif; ?>
        <section class="<?php assign(StaticHtmlEntries::$layout_classes[$data['staticHtmlEntry']['layout_type']]); ?>">

            <?php if(!Util::isSmartPhone()): // PC ?>
                <?php if (!$data['staticHtmlEntry']['title_hidden_flg'] && count($data['sns_plugin_ids'])): // タイトル表示／SNSプラグイン表示 ?>
                    <header>
                        <h1><?php assign($data['staticHtmlEntry']['title']) ?></h1>
                        <?php write_html($this->parseTemplate('PageContentHeader.php', array('sns_plugin_ids' => $data['sns_plugin_ids'], 'custom_plugin' => $data['staticHtmlEntry']['sns_plugin_tag_text']))); ?>
                    </header>
                <?php elseif ($data['staticHtmlEntry']['title_hidden_flg'] && count($data['sns_plugin_ids'])): // タイトル非表示／SNSプラグイン表示 ?>
                    <header>
                        <?php write_html($this->parseTemplate('PageContentHeader.php', array('sns_plugin_ids' => $data['sns_plugin_ids'], 'custom_plugin' => $data['staticHtmlEntry']['sns_plugin_tag_text']))); ?>
                    </header>
                <?php elseif (!$data['staticHtmlEntry']['title_hidden_flg'] && !count($data['sns_plugin_ids'])): // タイトル表示／SNSプラグイン非表示 ?>
                    <header>
                        <h1><?php assign($data['staticHtmlEntry']['title']) ?></h1>
                    </header>
                <?php else: // タイトル非表示／SNSプラグイン非表示?>
                <?php endif;?>
            <?php else: // SP ?>
                <?php if (!$data['staticHtmlEntry']['title_hidden_flg']): ?>
                    <header>
                        <h1><?php assign($data['staticHtmlEntry']['title']) ?></h1>
                    </header>
                <?php endif; ?>
            <?php endif;?>

            <?php if ($data['staticHtmlEntry']['write_type'] == StaticHtmlEntries::WRITE_TYPE_BLOG):?>
            <div class="ckeditorWrap">
                <?php write_html($data['staticHtmlEntry']['body'])?>

                <?php if ($data['staticHtmlEntry']['extra_body'] && $data['pageStatus']['isLogin']): ?>
                    <?php write_html($data['staticHtmlEntry']['extra_body'])?>
                <?php endif; ?>
                <!-- /.ckeditorWrap --></div>

            <?php if ($data['staticHtmlEntry']['extra_body'] && !$data['pageStatus']['isLogin']): ?>
                <?php write_html($this->parseTemplate('UserStaticHtmlLoginLimitButton.php')); ?>
            <?php endif; ?>

            <?php elseif ($data['staticHtmlEntry']['write_type'] == StaticHtmlEntries::WRITE_TYPE_TEMPLATE):?>
                <?php write_html(aafwWidgets::getInstance()->loadWidget("UserStaticHtmlTemplatePage")->render($data));?>
            <?php endif; ?>

            <?php if (Util::isSmartPhone() && count($data['sns_plugin_ids'])): ?>
                <?php write_html($this->parseTemplate('PageContentHeaderSP.php', array('sns_plugin_ids' => $data['sns_plugin_ids'], 'custom_plugin' => $data['staticHtmlEntry']['sns_plugin_tag_text']))); ?>
            <?php endif; ?>

            <?php if ($data['has_comment_option'] && !Util::isNullOrEmpty($data['comment_plugin'])): ?>
                <?php if (Util::isNullOrEmpty($data['comment_plugin']->id)): ?>
                    <?php if ($data['comment_plugin']->status == CommentPLugin::COMMENT_PLUGIN_STATUS_PUBLIC): ?>
                        <?php write_html($this->parseTemplate('plugin/CommentPluginWidgetPreview.php', array('comment_plugin' => $data['comment_plugin']))); ?>
                    <?php endif ?>
                <?php else: ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('CommentPluginWidget')->render(array('comment_plugin' => $data['comment_plugin']))) ?>
                <?php endif ?>
            <?php endif ?>

        <!-- /.contBoxMain--></section>

            <?php if ($data['staticHtmlEntry']['layout_type'] == StaticHtmlEntries::LAYOUT_NORMAL && count($this->current_category_posts)): ?>
                <section class="pageWrap">

                    <header>
                        <h1 style="font-size:16px">関連する記事</h1>
                    </header>

                    <ul class="pageList">

                        <?php foreach ($data['current_category_posts'] as $static_entry): ?>
                            <?php
                            if ($static_entry->og_image_url){
                                $img_url = $static_entry->og_image_url;
                            }elseif($this->brand->profile_img_url){
                                $img_url = $this->brand->profile_img_url;
                            }else{
                                $img_url = $this->setVersion('/img/dummy/02.jpg');
                            }
                            ?>
                            <li>
                                <a href="<?php write_html(Util::rewriteUrl('', 'page', array($static_entry->page_url))) ?>">
                                    <figure class="pageImg"><img src="<?php assign($img_url) ?>" alt="<?php assign($static_entry->title) ?>"></figure>
                                    <p class="pageData">
                                        <strong><?php assign($static_entry->title) ?></strong>
                                        <span class="description"><?php assign($this->cutLongText($static_entry->meta_description ? $static_entry->meta_description : str_replace('&nbsp;', '', $static_entry->body, $static_entry->body), 150)) ?></span>
                                        <?php if (!$data['hidden_date_flg']): ?>
                                            <span class="date"><?php assign(date('Y/m/d', strtotime($static_entry->public_date))) ?></span>
                                        <?php endif; ?>
                                    </p>
                                </a>
                            </li>

                        <?php endforeach; ?>

                        <!-- /.pageList --></ul>
                <!-- /.pageWrap--></section>
            <?php endif; ?>

        <?php if ($data['staticHtmlEntry']['layout_type'] != StaticHtmlEntries::LAYOUT_FULL): ?>
            <?php write_html($this->parseTemplate('PageNavigation.php', array('current_category_name' => $data['current_category_name'],
                'current_category_url' => $data['current_category_url'],
                'page_title' => $data['staticHtmlEntry']['title'],
                'father_category' => $data['father_category'],
                'grandfather_category' => $data['grandfather_category']))); ?>
        <?php endif; ?>

    <?php if ($data['staticHtmlEntry']['layout_type'] == StaticHtmlEntries::LAYOUT_NORMAL): ?>
        <!-- /.mainContWrap--></div>
    <?php endif; ?>

    <?php if ($data['staticHtmlEntry']['layout_type'] == StaticHtmlEntries::LAYOUT_NORMAL): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('PageTopSideCol')->render($data['pageStatus']))/*サイドカラム*/ ?>
    <?php endif; ?>

    <p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
    <!-- /.mainCol --></article>
<?php write_html($this->parseTemplate('BrandcoInstagramModal.php')); ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
