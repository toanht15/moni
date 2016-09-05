<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php assign($data['title'])?></title>

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

    <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">
</head>
<body>
