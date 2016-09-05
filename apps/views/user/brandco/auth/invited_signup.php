<?php write_html($this->parseTemplate('BrandcoInviteHeader.php', $data['pageStatus'])); ?>

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

<?php $param = array_merge($data['pageStatus'], array('script' => $data['script'])) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
