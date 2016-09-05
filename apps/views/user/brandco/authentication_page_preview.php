<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>プレビュー</title>

    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">

    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style.css'))?>">
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/admin.css'))?>">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <?php $favicon_url = $data['brand']->getFaviconUrl(); ?>
    <?php if ($favicon_url): ?>
        <link rel="icon" href="<?php assign($favicon_url); ?>">
    <?php else: ?>
        <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">
    <?php endif ?>

    <!-- site base setting -->
    <style>
        /* site background */
        html, body {
            height: 100%;
        }
    </style>
</head>
<body>
<header class="prevHead">
    <div class="wrap">
        <p>プレビュー中：<span>スマートフォン<a href="#" class="toggle_switch right jsModulePreviewSwitch" data-preview_url="<?php assign($this->preview_url); ?>">表示切り替え</a>PC</span></p>
        <!-- /.wrap --></div>
    <!-- /.prevHead --></header>

<article class="adminPrevBody">
    
    <div class="adminPrev_pc jsModulePreviewArea">
        <iframe src="<?php assign($this->preview_url); ?>" frameborder="0" name="page preview" class="jsPreviewFrame"></iframe>
    </div>
    <!-- /.adminPrevBody --></article>
</body>

<?php write_html($this->scriptTag('PreviewService'))?>

<script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/dest/lib-all.js'))?>"></script>
<?php write_html($this->scriptTag('unit', false))?>
<?php write_html($this->scriptTag('admin_unit', false))?>
</html>
