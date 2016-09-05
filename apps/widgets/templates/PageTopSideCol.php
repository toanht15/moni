<aside class="sideCol jsStamp">

    <?php
    //TODO UQハードコーディング
    if (!$data['is_uq_account']) {
        write_html($this->parseTemplate('SideColCategoriesBox.php', array('top_categories' => $data['top_categories'])));
    }
    ?>

    <?php
    //TODO 健康検定用のハードコーディング
    //TODO UQハードコーディング
        if ($data['brand']->id != Brand::KENKO_KENTEI_ID && !$data['is_uq_account']) {
            write_html($this->parseTemplate('SideColFanCounterBox.php', array('brand' => $data['brand'], 'brand_info' => $data['brand_info'], 'side_col_info' => $data['side_col_info'])));
        }
    ?>

    <?php if ($data['is_uq_account']): //TODO UQハードコーディング ?>
        <script src="//s3-ap-northeast-1.amazonaws.com/parts.brandco.jp/image/brand/f9b902fc3289af4dd08de5d1de54f68f/fan.uqwimax_page_files/js/sideNav.js" id="js--uqwimaxSideNav"></script>
        <script src="//s3-ap-northeast-1.amazonaws.com/parts.brandco.jp/image/brand/f9b902fc3289af4dd08de5d1de54f68f/fan.uqwimax_page_files/js/sideFreearea.js" id="js--uqwimaxSideFreearea"></script>
        <p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
    <?php else: ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('SideColSNSListBox')->render(array('brand_social_accounts' => $data['brand_social_accounts'],'brand' => $data['brand']))); ?>
    <?php endif; ?>

<!-- /.sideCol --></aside>