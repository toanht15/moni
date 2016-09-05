<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
