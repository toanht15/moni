<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html($this->parseTemplate('AgeAuthenticationHeader.php', $data['pageStatus'])) ?>

<article>
    <section class="pageAuthentication">
        <?php write_html($data['page_content']);?>
        <?php write_html($this->formHidden('is_preview', $data['is_preview'])); ?>
        <?php write_html($this->formHidden('no_link', $data['no_link'])); ?>
<!-- /.pageAuthentication --></SECTION>
<p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
<!-- /.mainCol --></article>
<?php write_html($this->scriptTag('AuthenticationPageService'))?>
<?php write_html($this->scriptTag('CookieService'))?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
