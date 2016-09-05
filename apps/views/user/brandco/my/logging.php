<?php write_html( aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus']) ) ?>

<?php write_html( aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus']) ) ?>

<article class="singleWrap">
    <?php write_html( aafwWidgets::getInstance()->loadWidget('BrandcoLoggingForm')->render(array(
        'pageStatus' => $data['pageStatus'],
        'pageInfo' => $data['pageInfo'],
        'loggingFormInfo' => $data['loggingFormInfo'],
        'ActionForm' => $this->ActionForm,
        'ActionError' => $this->ActionError
    ))); ?>
</article>

<?php write_html($this->parseTemplate('auth/CompletePasswordIssueModal.php')); ?>
<?php write_html( $this->parseTemplate('BrandcoFooter.php', $data['pageStatus']) ); ?>
