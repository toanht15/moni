<?php write_html($this->parseTemplate('BrandcoManagerInquiryHeader.php', array('title' => 'お問い合わせ一覧'))) ?>

<article>
    <h1 class="hd1">ユーザーからのお問い合わせ一覧</h1>

    <?php write_html($this->parseTemplate('InquirySearchForm.php', array('operator_type' => InquiryRoom::TYPE_MANAGER, 'total_count' => $data['total_count']))); ?>

    <section class="inquiryUserList jsInquiryList">
        <?php write_html($this->parseTemplate('InquiryList.php', array('operator_type' => InquiryRoom::TYPE_MANAGER, 'inquiry_list' => $data['inquiry_list'], 'page' => $data['page'], 'total_count' => $data['total_count']))); ?>
        <!-- /.inquiryUserList --></section>
        <?php write_html($this->parseTemplate('InquirySetting.php', array('operator_type' => InquiryRoom::TYPE_MANAGER, 'inquiry_sections' => $data['inquiry_sections']))); ?>

</article>

<?php write_html($this->parseTemplate('InquirySectionAddModal.php', array('operator_type' => InquiryRoom::TYPE_MANAGER))) ?>
<?php write_html($this->parseTemplate('InquirySectionDeleteModal.php', array('operator_type' => InquiryRoom::TYPE_MANAGER))) ?>
<?php write_html($this->parseTemplate('InquiryTemplateModal.php', array('operator_type' => InquiryRoom::TYPE_MANAGER, 'file_name' => 'show_inquiry_template_list'))) ?>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css')) ?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript"
        src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
<?php write_html($this->scriptTag('InquiryListService')) ?>
<?php write_html($this->scriptTag('InquirySectionService')) ?>
<?php write_html($this->scriptTag('InquiryTemplateService')) ?>
<?php write_html($this->parseTemplate('BrandcoManagerInquiryFooter.php')); ?>
