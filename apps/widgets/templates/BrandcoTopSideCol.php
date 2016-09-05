<aside class="sideCol jsStamp">

    <?php if (count($data['top_categories'])): ?>
        <?php write_html($this->parseTemplate('SideColCategoriesBox.php', array('top_categories' => $data['top_categories']))); ?>
    <?php endif; ?>

    <?php
    //TODO 健康検定用のハードコーディング
        if($data['brand']->id != Brand::KENKO_KENTEI_ID){
            write_html($this->parseTemplate('SideColFanCounterBox.php', array('brand' => $data['brand'], 'brand_info' => $data['brand_info'], 'side_col_info' => $data['side_col_info'])));
        }
    ?>

    <?php write_html(aafwWidgets::getInstance()->loadWidget('SideColSNSListBox')->render(array('brand_social_accounts' => $data['brand_social_accounts'],'brand' => $data['brand']))); ?>

    <?php write_html($this->parseTemplate('SideColMenuBox.php', array('brand' => $data['brand'], 'sideMenus' => $data['sideMenus'], 'isLoginAdmin' => $data['isLoginAdmin']))); ?>

<!-- /.sideCol --></aside>