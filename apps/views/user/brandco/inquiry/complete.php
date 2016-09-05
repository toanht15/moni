<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php if($data['skip_age_authentication']): ?>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>
<?php else: ?>
    <?php write_html($this->parseTemplate('AgeAuthenticationHeader.php', $data['pageStatus'])) ?>
<?php endif; ?>

<article>
    <section class="inquiryWrap">
        <h1 class="hd1">お問い合わせありがとうございました。</h1>
        <p>担当の者からお返事をお送りいたします。</p>
        <p>※混雑状況によっては返答にお時間をいただく場合がございます。あらかじめご了承ください。<br /><br /></p>
        <?php if($data['referer']): ?>
            <p class="btnSet"><span class="btn3"><a href="<?php assign($data['referer'])?>" class="large4">戻る</a></span></p>
        <?php endif; ?>
    </section>
</article>

<?php write_html($this->scriptTag('InquiryService')) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
