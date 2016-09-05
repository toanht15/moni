<?php write_html($this->parseTemplate('BrandcoManagerInquiryHeader.php', array('title' => 'お問い合わせ詳細'))) ?>

<article>
    <ul class="pager2">
        <li class="prev"><a href="<?php assign(Util::rewriteUrl(InquiryRoom::getDir(InquiryRoom::TYPE_MANAGER), 'show_inquiry_list', array(), array(), '', true)); ?>" class="iconPrev1">ユーザーからのお問い合わせ一覧</a></li>
         <!-- /.pager2 --></ul>
    <h1 class="hd1"><img src="<?php assign($data['inquiry_info']['sender_list'][InquiryMessage::ADMIN]['image']); ?>" width="40" height="40" alt="fansite name"><?php assign($data['inquiry_info']['sender_list'][InquiryMessage::ADMIN]['name']); ?>へのお問い合わせ</h1>

    <?php write_html($this->parseTemplate('InquiryUserDetail.php', $data['inquiry_info'])); ?>

    <section class="inquiryEditWrap">
        <?php write_html($this->parseTemplate('InquiryEdit.php', $data['inquiry_info'])); ?>
        <?php write_html($this->parseTemplate('InquiryChat.php', $data['inquiry_info'])); ?>
    </section>

    <ul class="pager2">
        <li class="prev"><a href="<?php assign(Util::rewriteUrl(InquiryRoom::getDir(InquiryRoom::TYPE_MANAGER), 'show_inquiry_list', array(), array(), '', true)); ?>" class="iconPrev1">ユーザーからのお問い合わせ一覧</a></li>
        <!-- /.pager2 --></ul>

    <h2 class="hd2">問い合わせ履歴</h2>
    <ul class="inquiryHistory">
        <?php foreach ($data['inquiry_info']['inquiry_history'] as $inquiry_history): ?>
            <li><a href="<?php assign(Util::rewriteUrl(InquiryRoom::getDir($data['inquiry_info']['operator_type']), 'show_inquiry', array($inquiry_history['id']), array(), '', true)); ?>"><span class="title"><?php assign(Util::cutTextByWidth($inquiry_history['content'], 800)); ?></span><small class="date"><?php assign(Util::getFormatDateString($inquiry_history['created_at'])); ?></small></a></li>
        <?php endforeach; ?>
        <!-- /.inquiryHistory --></ul>
</article>

<?php write_html($this->parseTemplate('InquirySectionAddModal.php', array('operator_type' => InquiryRoom::TYPE_MANAGER))); ?>
<?php write_html($this->parseTemplate('InquirySectionDeleteModal.php', array('operator_type' => InquiryRoom::TYPE_MANAGER))); ?>
<?php write_html($this->parseTemplate('InquiryTemplateModal.php', array('operator_type' => InquiryRoom::TYPE_MANAGER, 'file_name' => 'show_inquiry_template'))) ?>
<?php write_html($this->parseTemplate('InquiryMessageSaveModal.php')) ?>
<?php write_html($this->parseTemplate('InquiryForwardModal.php')) ?>

<?php write_html($this->scriptTag('InquiryRoomService')) ?>
<?php write_html($this->scriptTag('InquiryChatService')) ?>
<?php write_html($this->scriptTag('InquirySectionService')) ?>
<?php write_html($this->scriptTag('InquiryTemplateService')) ?>
<?php write_html($this->parseTemplate('BrandcoManagerInquiryFooter.php')); ?>
