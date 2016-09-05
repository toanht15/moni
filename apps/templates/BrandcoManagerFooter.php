	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="<?php assign($this->setVersion('/manager/js/bootstrap.min.js'))?>"></script>
	<script src="<?php assign($this->setVersion('/manager/js/docs.min.js'))?>"></script>
    <script src="<?php assign($this->setVersion('/manager/js/tooltip.js'))?>"></script>
    <script src="<?php assign($this->setVersion('/manager/js/services/BrandListService.js'))?>"></script>

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

    <?php if(config('Analytics.Status')): ?>
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-61362709-1', 'auto');
            ga('send', 'pageview');

        </script>
    <?php endif; ?>
</body>
</html>
