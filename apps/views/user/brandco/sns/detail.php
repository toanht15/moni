<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

<article class="mainCol">

    <?php write_html($this->parseTemplate('SnsPageAccountInfo.php', $this->brand_social_account)); ?>

    <div class="snsPageWrap">
        <?php write_html($this->parseTemplate('SnsPageContent' . $this->page_data['sns_type'] . '.php',  array('sns_entry' => $this->sns_entry, 'brand_social_account' => $this->brand_social_account))); ?>

        <?php if ($this->page_data['prev_url'] || $this->page_data['next_url']): ?>
            <ul class="pager3">
                <?php if ($this->page_data['prev_url']): ?>
                    <li><a href="<?php write_html($this->page_data['prev_url']); ?>" class="iconPrev1">前の記事</a></li>
                <?php endif; ?>
                <?php if ($this->page_data['next_url']): ?>
                    <li><a href="<?php write_html($this->page_data['next_url']); ?>" class="iconNext1">次の記事</a></li>
                <?php endif; ?>
                <!-- /.pager3 --></ul>
        <?php endif; ?>

        <!-- /.snsPageWrap --></div>

    <?php if ($data['brand']->id == 335)://UQハードコーディング ?>
        <p class="pagePrev"><a href="<?php assign(Util::rewriteUrl('page', 'sns')); ?>">一覧へ戻る</a></p>
    <?php else: ?>
        <p class="pagePrev"><a href="<?php assign(Util::rewriteUrl('', '')); ?>">一覧へ戻る</a></p>
    <?php endif; ?>

    <nav class="bredlink1">
        <ul>
            <li class="home"><a href="<?php assign(Util::rewriteUrl('', '')); ?>">HOME</a></li>
            <li><a href="<?php assign(Util::rewriteUrl('sns', 'category', array($this->brand_social_account->id))); ?>"><?php assign('[' . $this->page_data['sns_name'] . '] ' . $this->page_data['page_name']); ?></a></li>
            <li class="current"><span><?php assign($this->page_data['page_title']); ?></span></li>
        </ul>
        <!-- /.bredlink1 --></nav>

    <p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
    <!-- /.mainCol --></article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
