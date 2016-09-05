<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php if($data['skip_age_authentication']): ?>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>
<?php else: ?>
    <?php write_html($this->parseTemplate('AgeAuthenticationHeader.php', $data['pageStatus'])) ?>
<?php endif; ?>

<article>
    <h1 class="hd1_inquiry"><img src="<?php assign($data['brand']->getProfileImage()); ?>" width="40" height="40" alt="fansite name"><?php assign($data['brand']->name); ?>へのお問い合わせ</h1>

    <div class="inquiryUserView">
        <section class="inquiryEditWrap">
            <?php write_html($this->parseTemplate('InquiryChat.php', $data['inquiry_info'])); ?>
            <!-- /.inquiryEditWrap --></section>
        <!-- /.inquiryUserView --></div>
</article>

<?php write_html($this->parseTemplate('InquiryMessageSaveModal.php')) ?>

<?php write_html($this->scriptTag('InquiryChatService')) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
