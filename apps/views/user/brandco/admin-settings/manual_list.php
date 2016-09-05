<?php write_html($this->parseTemplate('BrandcoPopupHeader.php', $data['pageStatus'])); ?>

<body class="markdownListWrap">
<article>
    <h1 class="hd1">マニュアルダウンロード</h1>
    <h2 class="hd2">ファンサイト構築マニュアル<small>(PDF)</small></h2>
    <section>
        <ul class="manualList">
            <?php if(!$data['manuals_cms']): ?>
                <li>マニュアルはありません</li>
            <?php else: ?>
                <?php foreach($data['manuals_cms'] as $manual): ?>
                    <li><a href="<?php assign($manual->url)?>" target="_blank" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'manual', '<?php assign($manual->title) ?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});"><?php assign($manual->title) ?></a></li>
                <?php endforeach; ?>
            <?php endif; ?>
            <!-- /.manualList --></ul>
    </section>
    <h2 class="hd2">キャンペーン作成マニュアル<small>(PDF)</small></h2>
    <section>
        <ul class="manualList">
            <?php if(!$data['manuals_campaign']): ?>
            <li>マニュアルはありません</li>
            <?php else: ?>
                <?php foreach($data['manuals_campaign'] as $manual): ?>
                    <li><a href="<?php assign($manual->url)?>" target="_blank" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'manual', '<?php assign($manual->title) ?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});"><?php assign($manual->title) ?></a></li>
                <?php endforeach; ?>
            <?php endif; ?>
            <!-- /.manualList --></ul>
    </section>
</article>
<?php write_html($this->parseTemplate('GoogleAnalytics.php', $data)); ?>
<!-- /.markdownListWrap --></body>
