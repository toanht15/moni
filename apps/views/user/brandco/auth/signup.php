<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

<article class="singleWrap">
    <?php if ($data['template_name'] === 'UserProfileForm'): ?>
        <?php write_html($this->parseTemplate('auth/UserProfileForm.php', array(
            'parent_class_name' => 'singleWrap',
            'brand' => $data['brand'],
            'pageStatus' => $data['pageStatus'],
            'ActionForm' => $this->ActionForm,
            'ActionError' => $this->ActionError
        ))); ?>
    <?php else: ?>
        <?php write_html( aafwWidgets::getInstance()->loadWidget('BrandcoSignupForm')->render(array(
            'parent_class_name' => 'singleWrap',
            'brand' => $data['brand'],
            'pageStatus' => $data['pageStatus'],
            'ActionForm' => $this->ActionForm,
            'ActionError' => $this->ActionError
        ))); ?>
    <?php endif; ?>
    <!-- /.singleWrap --></article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
