<img id="ajaxReloadBox" src="<?php assign($this->setVersion('/img/base/ajax-loader.gif'))?>" width="30" height="30" style="display:none" />

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

<?php if ($data['pageStatus']['isLoginAdmin']): ?>
    <?php write_html($this->scriptTag('admin_unit', false)) ?>
<?php endif; ?>

<?php write_html($this->scriptTag('html5shiv-printshiv', false))?>

<?php if (Util::isSmartPhone()): ?>
    <?php write_html($this->scriptTag('unit_sp', false)) ?>
<?php else: ?>
    <?php write_html($this->scriptTag('unit', false)) ?>
<?php endif; ?>

<?php write_html($this->scriptTag('jquery.blockUI', false))?>

<?php if($_GET['mid'] && $data['isLoginAdmin']): ?>
    <section class="noticeBar1 jsNoticeBarArea1" id="mid-message">
        <p class="<?php assign(config('@message.adminMessage.'.$_GET['mid'].'.class')) ?> jsNoticeBarClose" id="jsMessage1"><?php assign(config('@message.adminMessage.'.$_GET['mid'].'.msg')) ?></p>
    </section>
    <script type="text/javascript">
        $('article').each(function(){
            $(this).prepend($('#mid-message'));
            return false;
        });
        Brandco.unit.showNoticeBar($('#jsMessage1'));
    </script>
<?php endif; ?>

<?php if($data['script']): ?>
    <?php foreach($data['script'] as $script): ?>
        <?php write_html($this->scriptTag($script)); ?>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>