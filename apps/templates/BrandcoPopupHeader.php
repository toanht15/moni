<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php assign($data['brand']->name)?></title>

    <?php if (extension_loaded ('newrelic')) {
        $config = aafwApplicationConfig::getInstance();
        if($config->NewRelic['use']) {
            write_html(newrelic_get_browser_timing_header());
        }
    } ?>

    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">

    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style.css')) ?>">
    <?php if(Util::isSmartPhone()):?>
        <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style_sp.css'))?>">
    <?php endif;?>
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/admin.css')) ?>">

    <!--[if lt IE 9]>
    <script src="<?php assign($this->setVersion('/js/html5shiv-printshiv.js')); ?>"></script>
    <![endif]-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="<?php assign($this->setVersion('/js/min/imagesloaded.pkgd.min.js'))?>"></script>

    <?php $favicon_url = $data['brand']->getFaviconUrl(); ?>
    <?php if ($favicon_url): ?>
        <link rel="icon" href="<?php assign($favicon_url); ?>">
    <?php else: ?>
        <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">
    <?php endif ?>
</head>
<body>
