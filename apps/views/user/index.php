<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>モニプラ</title>

    <meta name="description" content="BRANDCo（ブランコ） -ブランドと新しいつきあいへ-">
    <meta name="keywords" content="キャンペーン,ブランド,新着情報">
    <meta name="wot-verification" content="41454b4377cf0746125d"/>
    <meta property="og:url" content="http://monipla.com">
    <meta property="og:image" content="<?php assign($this->setVersion('/top/img/base/ogimg.png'))?>">
    <meta property="og:site_name" content="BRANDCo（ブランコ）">
    <meta property="og:title" content="BRANDCo（ブランコ） -ブランドと新しいつきあいへ-">
    <meta property="og:description" content="BRANDCo（ブランコ）は、ブランドのSNSアカウントの記事をまとめて確認できたり、どなたでも参加できるキャンペーン情報をお届けします">
    <meta property="og:type" content="article">

    <link rel="icon" href="<?php assign($this->setVersion('/top/img/base/favicon.ico'))?>">
    <link rel="stylesheet" href="<?php assign($this->setVersion('/top/css/style.css'))?>">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <!--[if lt IE 9]>
    <script src="https://github.com/aFarkas/html5shiv/blob/master/dist/html5shiv.min.js"></script>
    <script src="<?php assign($this->setVersion('/js/css3-mediaqueries.js'))?>"></script>
    <script src="<?php assign(config("Static.Url")) ?>/js/html5shiv-printshiv.js"></script>
    <![endif]-->
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
</head>
<body id="Top">
<div id="fb-root"></div>
<script>
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&appId=<?php assign(config("@facebook.Admin.AppId")); ?>&version=v2.6";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
    (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/plusone.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
</script>

<section class="mainVisualWrap">
    <div class="wrap">
        <h1 class="mainLogo"><img src="<?php assign($this->setVersion('/top/img/base/mainLogo.png'))?>" alt="BRANDCo"></h1>
        <h2>ブランドと新しいつきあいへ</h2>
        <p class="lead">BRANDCo（ブランコ）は、あなたの好きなブランドの「コミュニケーションサイト」閲覧や、様々なキャンペーンへの参加が可能なサービスです。ブランドからのお得な最新情報を、BRANDCoで手に入れましょう。</p>

        <p class="mainVisual"><img src="<?php assign($this->setVersion('/top/img/base/mv2.png'));?> alt=""></p>

        <ul class="snsBtns displaySp">
            <li><div class="fb-like" data-href="<?php assign(Util::getBaseUrl(true))?>" data-layout="button" data-action="like" data-show-faces="false" data-share="false"></div></li
                ><li><a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-count="none">ツイート</a></li
                ><li><div class="g-plusone" data-size="tall" data-annotation="none"></div></li>
        </ul>
        <!-- /.wrap --></div>
    <!-- /.mainVisualWrap --></section>

<main class="point">
    <div class="wrap">
        <h1><span>What's BRANDCo?</span></h1>
        <ul class="PointList cf">
            <li>
                <h2><span class="PontIcon"><img src="<?php assign($this->setVersion('/top/img/base/IconPont1.png'))?>" alt="1"/></span><br>複数のSNSアカウントを、BRANDCoの<br>コミュニケーションサイトでまとめて確認できます</h2>
                <p class="PontImg"><img src="<?php assign($this->setVersion('/top/img/base/Point1.png'))?>" alt=""/></p>
            </li>
            <li>
                <h2><span class="PontIcon"><img src="<?php assign($this->setVersion('/top/img/base/IconPont2.png'))?>" alt="2"/></span><br>BRANDCoではSNSアカウント、メールアドレス、<br>どなたでもキャンペーンに参加できます</h2>
                <p class="PontImg"><img src="<?php assign($this->setVersion('/top/img/base/Point2.png'))?>" alt=""/></p>
            </li>
        </ul>
    </div>
</main>

<div class="displayPc">
    <p class="toTop"><a href="#Top" class="toTopLink">PAGE TOP</a></p>

    <ul class="snsBtns">
        <li><div class="fb-like" data-href="https://monipla.com/" data-layout="button" data-action="like" data-show-faces="false" data-share="false"></div></li
            ><li><a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-count="none">ツイート</a></li
            ><li><div class="g-plusone" data-size="tall" data-annotation="none"></div></li>
    </ul>
</div>

<p class="BizBnr"><a href="https://www.aainc.co.jp/service/brandco/" target="_blank"><img src="<?php assign($this->setVersion('/top/img/base/Bnr_BizLP.png'))?>" alt=""/></a></p>

<footer>
    <div class="wrap">
        <div class="cf">
            <p class="AAlogo"><img src="<?php assign($this->setVersion('/top/img/base/LogoAA.gif'))?>"  alt=""/></p>
            <ul class="AALink">
                <li><a href="http://www.aainc.co.jp" target="_blank">運営会社</a></li>

                <li><a href="http://www.aainc.co.jp/policy/" target="_blank">プライバシーポリシー</a></li>

                <li><a href="http://allied-id.com/maintenance" target="_blank">メンテナンス情報</a></li>
            </ul>
        </div>
        <p class="copy"><small>Copyright &copy; 2014 Allied Architects, Inc. All Rights Reserved.</small></p>
        <!-- /.wrap --></div>
</footer>

<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-48876215-1', 'monipla.com');
    ga('send', 'pageview');
</script>

</body>
</html>