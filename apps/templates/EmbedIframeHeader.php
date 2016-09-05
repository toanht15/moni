<!doctype html>
<html lang="ja" class="embedPageBody">
<head>
	<meta charset="UTF-8">
	<title><?php assign($data['brand']->name)?></title>

    <?php if (extension_loaded ('newrelic')) {
        $config = aafwApplicationConfig::getInstance();
        if($config->NewRelic['use']) {
            write_html(newrelic_get_browser_timing_header());
        }
    } ?>

	<link rel="stylesheet" href="<?php assign($this->setVersion('/css/style.css'))?>">
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style_sp.css'))?>">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

    <?php $favicon_url = $data['brand']->getFaviconUrl(); ?>
    <?php if ($favicon_url): ?>
        <link rel="icon" href="<?php assign($favicon_url); ?>">
    <?php else: ?>
        <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">
    <?php endif ?>

    <base href="<?php assign(Util::getBaseUrl()) ?>" data-static-href="<?php assign(config('Static.Url')) ?>">
</head>
<body style="background-position: 0px 31px;" class="ownedPageBody">
