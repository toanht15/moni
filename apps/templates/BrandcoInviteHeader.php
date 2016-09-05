<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php assign($data['pageStatus']['brand']->name)?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">

    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style.css'))?>">
    <?php if(Util::isSmartPhone()):?>
        <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style_sp.css'))?>">
    <?php endif;?>

    <?php if ($data['is_olympus_header_footer']): ?>
        <link rel="stylesheet" href="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/css/style.css'))?>">
        <?php if(Util::isSmartPhone()):?>
            <link rel="stylesheet" href="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/css/style_sp.css'))?>">
        <?php endif;?>
    <?php endif ?>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <?php $favicon_url = $data['brand']->getFaviconUrl(); ?>
    <?php if ($favicon_url): ?>
        <link rel="icon" href="<?php assign($favicon_url); ?>">
    <?php else: ?>
        <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">
    <?php endif ?>

</head>
<body>
<header class="editHeadWrap">
    <section class="editHead">
        <div class="wrap">
            <h1><a href="<?php assign(Util::getHttpProtocol().'://'.  Util::getMappedServerName()) ?>"><img src="<?php assign($this->setVersion('/img/base/imgLogo_w.png'))?>" width="108" height="18" alt="モニプラ"></a></h1>
            <!-- /.wrap --></div>
        <!-- /.editHead --></section>

    <!-- /.editHead --></header>
