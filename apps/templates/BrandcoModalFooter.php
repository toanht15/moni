    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/html5shiv-printshiv.js'))?>"></script>
    <![endif]-->
    <script src="<?php assign($this->setVersion('/js/masonry.pkgd.min.js'))?>"></script>
    <script src="<?php assign($this->setVersion('/js/min/imagesloaded.pkgd.min.js'))?>"></script>

    <?php if(DEBUG): ?>
        <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.js'))?>"></script>
        <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.net.js'))?>"></script>
        <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.api.js'))?>"></script>
        <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.message.js'))?>"></script>
        <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.helper.js'))?>"></script>
    <?php else: ?>
        <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/dest/lib-all.js'))?>"></script>
    <?php endif; ?>
    <img id="ajaxReloadBox" src="<?php assign($this->setVersion('/img/base/ajax-loader.gif'))?>" width="30" height="30" style="display:none" />
    <script src="<?php assign($this->setVersion('/js/unit.js'))?>"></script>
    <script src="<?php assign($this->setVersion('/js/admin_unit.js'))?>"></script>
    <script src="<?php assign($this->setVersion('/js/jquery.blockUI.js'))?>"></script>

    <?php if($_GET['mid']): ?>
        <section class="noticeBar1 jsNoticeBarArea1" id="mid-message">
            <p class="<?php assign(config('@message.noticeMessage.'.$_GET['mid'].'.class')) ?> jsNoticeBarClose" id="jsMessage1"><?php assign(config('@message.noticeMessage.'.$_GET['mid'].'.msg')) ?></p>
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

    <?php write_html($this->parseTemplate('GoogleAnalytics.php'));?>
    <?php write_html($this->parseTemplate('Rtoaster.php'));?>

    <?php if (extension_loaded ('newrelic')) {
        $config = aafwApplicationConfig::getInstance();
        if($config->NewRelic['use']) {
            write_html(newrelic_get_browser_timing_footer());
        }
    } ?>

</body>
</html>