<?php if (config('Stage') === 'product' && config('Analytics.Status') && config('Analytics.ID') && Util::isAcceptRemote()): ?>
    <?php write_html($this->scriptTag('GoogleAnalyticsTrackingService')); ?>

    <script>
        var ga_param = {};
        <?php if ($data["path"]): ?>
            ga_param.page = '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']." (".$data["path"].")") ?>';
        <?php else: ?>
            ga_param.page = '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>';
        <?php endif; ?>

        <?php if ($data["isLogin"] && $data["userInfo"]->id): ?>
            ga_param.userId = '<?php assign($data["userInfo"]->id) ?>';
        <?php endif; ?>

        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        GoogleAnalyticsTrackingService.generate("<?php assign(config('Analytics.ID')) ?>", "<?php assign(config('Analytics.TrackerName')) ?>", ga_param);
    </script>
<?php endif; ?>
