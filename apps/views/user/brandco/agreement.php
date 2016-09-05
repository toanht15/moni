<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php if($data['skip_age_authentication']): ?>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>
<?php else: ?>
    <?php write_html($this->parseTemplate('AgeAuthenticationHeader.php', $data['pageStatus'])) ?>
<?php endif; ?>

<article>
    <section class="singleWrap">
        <h1 class="hd1">利用規約</h1>
        <section class="agreementCont">
            <?php write_html($this->nl2brAndHtmlspecialchars($data['agreement'])); ?>
            <!-- /.agreementCont --></section>
        <!-- /.singleWrap --></section>
    <p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
<!-- /.mainCol --></article>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>

