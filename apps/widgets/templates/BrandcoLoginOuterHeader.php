<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php assign($data['title'])?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="keyword" content="<?php assign($data['keyword']);?>">
    <base href="<?php assign(Util::getBaseUrl()) ?>" data-static-href="<?php assign(config('Static.Url')) ?>">

    <?php if (extension_loaded ('newrelic')) {
        if(config('NewRelic.use')) {
            //write_html(newrelic_get_browser_timing_header());
        }
    } ?>

    <?php foreach($data['og'] as $property => $content):?>
        <meta property="og:<?php assign($property);?>" content="<?php assign($property == 'image' ? Util::convertProxyURL($content) : $content );?>">
        <?php if($property == 'description'): ?>
            <meta name="description" content="<?php assign($content);?>">
        <?php endif;?>
    <?php endforeach;?>

    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style.css'))?>">
    <?php if(Util::isSmartPhone()):?>
        <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style_sp.css'))?>">
    <?php endif;?>
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/admin.css'))?>">

    <!--[if lt IE 9]>
    <script src="<?php assign($this->setVersion('/js/html5shiv-printshiv.js')); ?>"></script>
    <![endif]-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion_async.js" charset="utf-8"></script>
    <script src="<?php assign($this->setVersion('/js/min/imagesloaded.pkgd.min.js'))?>"></script>
    <script src="<?php assign($this->setVersion('/js/jquery.form.js'))?>"></script>
    <?php $favicon_url = $data['brand']->getFaviconUrl(); ?>
    <?php if ($favicon_url): ?>
        <link rel="icon" href="<?php assign($favicon_url); ?>">
    <?php else: ?>
        <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">
    <?php endif ?>
</head>
<body>
<div id="fb-root"></div>
<script>
    (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/plusone.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
    !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
</script>

<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

<header class="editHeadWrap">
    <section class="editHead">
        <div class="wrap">
            <h1>
                <img src="<?php assign($this->setVersion('/img/base/imgLogo_w.png'))?>" width="108" height="18" alt="モニプラ">
            </h1>
        <!-- /.wrap --></div>
    <!-- /.editHead --></section>
<section class="account">
    <ul>
        <li class="accountCompany">
            <img src="<?php assign($data['brand']->getProfileImage())?>" width="130" height="130" alt=""><?php assign($data['brand']->name); ?> 
        </li>
    </ul>
<!-- /.account --></section>
<!-- /.editHead --></header>
