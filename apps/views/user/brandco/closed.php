<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

<article class="mainCol">

    <section class="siteClose">
        <?php if ($data['brand_contract']['closed_title']): ?>
            <h1><?php assign($data['brand_contract']['closed_title']) ?></h1>
        <?php endif; ?>
        <?php write_html($data['brand_contract']['closed_description']); ?>
        <!-- /.siteClose --></section>

    <p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
    <!-- /.mainCol --></article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', array($data['pageStatus']))) ?>