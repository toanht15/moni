<!doctype html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Not Found</title>

  <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">

  <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style.css'))?>">
  <?php if(Util::isSmartPhone()):?>
  <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style_sp.css'))?>">
  <?php endif;?>

  <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">

</head>
<body>
  <article>
    <section class="notFound">
      <h1><strong>404</strong>Not Found<small>お探しのページは見つかりません</small></h1>
      <p>お探しのページはアクセスできない状況か、移動・削除された可能性があります。<br>また、URLの入力ミスがないかご確認ください。</p>
      <p class="btnSet"><span class="btn1"><a href="http://<?php assign(Util::getMappedServerName())?>">TOPページへ</a></span></p>
    <!-- /.notFound --></section>
  <!-- /.wrap --></article>
  <footer>
    <div class="copyright">
      <small>Copyright © <?php assign(date('Y'));?> Allied Architects. ALL RIGHTS RESERVED</small>
    </div>

    <ul class="links">
      <li><a href="<?php assign('//'.config('Domain.aaid'))?>/agreement" target="_blank">アライドID利用規約</a></li>
      <li><a href="http://www.aainc.co.jp/privacy/" target="_blank">個人情報保護方針</a></li>
      <li><a href="http://allied-id.com/maintenance" target="_blank">メンテナンス情報</a></li>
    </ul>

    <p class="poweredBy"><small>Powered by monipla</small></p>
  </footer>
<?php write_html($this->parseTemplate('GoogleAnalytics.php', array("path" => "404")));?>
</body>
</html>