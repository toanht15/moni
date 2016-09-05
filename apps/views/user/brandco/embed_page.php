<!doctype html>
<html lang="ja" class="embedPageBody">
<head>
    <meta charset="UTF-8">
    <?php if (extension_loaded ('newrelic')) {
        $config = aafwApplicationConfig::getInstance();
        if($config->NewRelic['use']) {
            write_html(newrelic_get_browser_timing_header());
        }
    } ?>
</head>
<body>
<?php write_html($this->formHidden('base_url',Util::getBaseUrl()))?>
<?php write_html($this->formHidden('page_url',$data['pageUrl']))?>
<?php write_html($data['staticHtmlEntry']->body)?>
<?php write_html($this->scriptTag('admin-blog/EmbedIframeControllService'))?>
</body>
<?php write_html($this->parseTemplate('GoogleAnalytics.php'));?>
<?php if (extension_loaded ('newrelic')) {
    $config = aafwApplicationConfig::getInstance();
    if($config->NewRelic['use']) {
        write_html(newrelic_get_browser_timing_footer());
    }
} ?>
</html>