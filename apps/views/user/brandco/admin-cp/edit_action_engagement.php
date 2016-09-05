<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<?php $disable = ($data['status']== CpAction::STATUS_FIX)?'disabled':''  ?>

<article>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('CreateCpActionHeader')->render(array(
            'cp_id' => $data['cp_id'],
            'setting_id' => Cp::CP_SETTING_BASIC,
            'success' => $this->params['success'])
    )); ?>

<!-- /.wrap --></article>

<?php $script = array('admin-cp/EditActionService'); ?>
<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
