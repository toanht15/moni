<br><br>
<img id="ajaxReloadBox" src="<?php assign($this->setVersion('/img/base/ajax-loader.gif'))?>" width="30" height="30" style="display:none" />
<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>

<?php if(DEBUG): ?>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.net.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.api.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.message.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.helper.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.paging.js'))?>"></script>

<?php else: ?>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/dest/lib-all.js'))?>"></script>
<?php endif; ?>

<?php write_html($this->scriptTag('admin_unit', false))?>
<?php write_html($this->scriptTag('html5shiv-printshiv', false))?>

<?php if(Util::isSmartPhone()):?>
    <?php write_html($this->scriptTag('unit_sp', false))?>
<?php else:?>
    <script src="<?php assign($this->setVersion('/js/masonry.pkgd.min.js'))?>"></script>
    <?php if (Util::isSnsCategoryUrl()): ?>
        <?php write_html($this->scriptTag('BrandcoMasonryCategoryService'))?>
    <?php else: ?>
        <?php write_html($this->scriptTag('BrandcoMasonryTopService'))?>
    <?php endif; ?>
    <?php write_html($this->scriptTag('unit', false))?>
<?php endif;?>

<?php write_html($this->scriptTag('jquery.blockUI', false))?>

</body>
</html>

