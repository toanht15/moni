<?php write_html($this->parseTemplate('BrandcoInviteHeader.php', $data['pageStatus'])); ?>

<article class="singleWrap">
    <?php write_html( aafwWidgets::getInstance()->loadWidget('BrandcoLoggingForm')->render(array(
        'pageStatus' => $data['pageStatus'],
        'pageInfo' => $data['pageInfo'],
        'loggingFormInfo' => $data['loggingFormInfo'],
        'ActionForm' => $this->ActionForm,
        'ActionError' => $this->ActionError
    ))); ?>
</article>

<?php $param = array_merge($data['pageStatus'], array('script' => $data['script'])) ?>
<?php write_html($this->parseTemplate('auth/CompletePasswordIssueModal.php')); ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>