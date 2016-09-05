<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>プレビュー</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style.css'))?>">
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/admin.css'))?>">

</head>
<body>

<article>
    <section class="infomationDetail">
        <div class="ingfomationBody">
            <p><?php write_html($data['add_brand_information']['contents'])?></p>
        </div>
        <!-- /.infomationDetail --></section>

</article>
</body>
</html>