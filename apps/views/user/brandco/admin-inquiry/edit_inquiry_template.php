<?php write_html($this->parseTemplate('BrandcoManagerInquiryModalHeader.php', array('title' => 'お問い合わせテンプレート'))) ?>
<?php write_html($this->parseTemplate('InquiryEditTemplate.php',$data['inquiry_template_info'])) ?>
<?php write_html($this->scriptTag('InquiryTemplateModalService')) ?>
<?php write_html($this->parseTemplate('BrandcoManagerInquiryModalFooter.php')) ?>