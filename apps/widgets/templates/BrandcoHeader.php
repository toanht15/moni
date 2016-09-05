<!doctype html>
<html lang="ja">
<head>
    <?php if ($_GET["demo_token"]): ?>
        <meta name="robots" content="noindex,nofollow" />
    <?php endif; ?>

    <meta charset="UTF-8">
    <title><?php assign($data['title'])?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=2.0">
    <meta name="keyword" content="<?php assign($data['keyword']);?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@monipla_tw">
    <meta name="twitter:creator" content="@monipla_tw">
    <meta name="twitter:domain" content="monipla.com">

    <?php if ($data['is_olympus_header_footer']): ?>
        <meta name="google-site-verification" content="LbfG9tk1IKAq4h-S7ZLQkudC4Sr9U1hkgSSd5eZpHKU">
    <?php endif ?>

    <?php if($data['brand']->id == Brand::KENKO_KENTEI_ID): ?>
        <?php //TODO kenkenのハードコーディング ?>
        <meta name="google-site-verification" content="Nxdib5q2wUvwd4jrLJZIaN27veeheYk1Uu-eZVXXJYM" />
    <?php endif; ?>

    <?php if($data['brand']->id == Brand::JR_ODEKAKE_NET): ?>
        <?php //TODO jr_odekake_netのハードコーディング ?>
        <meta name="google-site-verification" content="QpkmYQuze59engQEUtR2-2Ji-2X1qioqyi3I-lP-31A" />
    <?php endif; ?>
    
    <?php if($data['brand']->id == '466'): ?>
        <?php //TODO sugaotaikenのハードコーディング ?>
        <meta name="google-site-verification" content="PUgXFTD44jMvkbeUndr334huv62Hhy6a_qaDakZw3WM" />
        <meta name="google-site-verification" content="j0r-5Zk7Rqa6jDpDlIEcJ2zjJ30HVVHTTWyb_VGbjDQ" />
    <?php endif; ?>

    <base href="<?php assign(Util::getBaseUrl()) ?>" data-static-href="<?php assign(config('Static.Url')) ?>">

    <?php if (extension_loaded ('newrelic')) {
        if(config('NewRelic.use')) {
            // write_html(newrelic_get_browser_timing_header());
        }
    } ?>

    <?php foreach($data['og'] as $property => $content):?>
        <meta property="og:<?php assign($property);?>" content="<?php assign($property == 'image' ? Util::convertProxyURL($content) : $content );?>">
        <?php if($property == 'description'): ?>
            <meta name="description" content="<?php assign($content);?>">
        <?php endif;?>
    <?php endforeach;?>

    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/bi/bi-style.css'))?>">
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style.css'))?>">
    <?php // TODO design confirm ?>
    <?php if(Util::isSmartPhone() || $data['is_cmt_plugin_mode']):?>
        <link rel="stylesheet" href="<?php assign($this->setVersion('/css/style_sp.css'))?>">
    <?php endif;?>

    <?php if($data['isLoginAdmin']):?>
        <link rel="stylesheet" href="<?php assign($this->setVersion('/css/admin.css'))?>">
    <?php endif;?>

    <?php if ($data['is_olympus_header_footer']): ?>
        <link rel="stylesheet" href="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/css/style.css'))?>">
        <?php if(Util::isSmartPhone()):?>
            <link rel="stylesheet" href="<?php assign($this->setVersion('/brand/voicerecorder.olympus-imaging.com/css/style_sp.css'))?>">
        <?php endif;?>
    <?php endif ?>

    <?php if ($data['is_whitebelg_header_footer']): ?>
        <link rel="stylesheet" href="<?php assign($this->setVersion('/brand/whitebelg/css/style.css'))?>">
        <?php if(Util::isSmartPhone()):?>
            <link rel="stylesheet" href="<?php assign($this->setVersion('/brand/whitebelg/css/style_sp.css'))?>">
        <?php endif;?>
    <?php endif ?>

    <?php if ($data['is_kenken_header_footer']): ?>
        <link rel="stylesheet" href="<?php assign($this->setVersion('/brand/kenken.or.jp/css/style.css'))?>">
        <?php if(Util::isSmartPhone()):?>
            <link rel="stylesheet" href="<?php assign($this->setVersion('/brand/kenken.or.jp/css/style_sp.css'))?>">
        <?php endif;?>
    <?php endif ?>

    <?php if ($data['is_uq_account']): ?>
        <link rel="stylesheet" href="<?php assign($this->setVersion('/brand/fan.uqwimax.jp/css/style.css'))?>">
        <?php if(Util::isSmartPhone()):?>
            <link rel="stylesheet" href="<?php assign($this->setVersion('/brand/fan.uqwimax.jp/css/style_sp.css'))?>">
        <?php endif;?>
        <?php if (!$data['is_uq_header_footer']): ?>
            <link rel="stylesheet" href="<?php assign($this->setVersion('/brand/fan.uqwimax.jp/css/admin.css'))?>">
        <?php endif;?>
    <?php endif ?>

    <!--[if lt IE 9]>
    <script src="<?php assign($this->setVersion('/js/html5shiv-printshiv.js')); ?>"></script>
    <![endif]-->
    <?php //個人情報端末の場合は、外部のjqueryを読み込めない ?>
    <?php if(Util::isPersonalMachine()): ?>
        <script src="<?php assign($this->setVersion('/js/min/jquery-1.10.2.min.js'))?>"></script>
    <?php else: ?>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <?php endif; ?>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion_async.js" charset="utf-8"></script>
    <script src="<?php assign($this->setVersion('/js/min/imagesloaded.pkgd.min.js'))?>"></script>
    <script src="<?php assign($this->setVersion('/js/jquery.form.js'))?>"></script>
    <?php if(Util::isSmartPhone()):?>
        <script src="<?php write_html($this->setVersion("/js/syn/synapse.min.js", false)); ?>"></script>
    <?php endif;?>
    <?php $favicon_url = $data['brand']->getFaviconUrl(BrandInfoContainer::getInstance()->getBrandPageSetting()); ?>
    <?php if ($favicon_url): ?>
        <link rel="icon" href="<?php assign($favicon_url); ?>">
    <?php else: ?>
        <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico')); ?>">
    <?php endif ?>
    <?php if(BrandInfoContainer::getInstance()->getBrandPageSetting()->rtoaster != ''):?>
        <script type="text/javascript" src="//js.rtoaster.jp/Rtoaster.js"></script>
    <?php endif;?>

    <?php if ($data['is_kenken_header_footer']): ?>
        <script src="<?php assign($this->setVersion('/brand/kenken.or.jp/js/util.js'))?>"></script>
    <?php endif ?>

    <?php if ($data['is_uq_header_footer']): ?>
        <script src="<?php assign($this->setVersion('/brand/fan.uqwimax.jp/js/util.js'))?>"></script>
    <?php endif ?>

    <?php if($data['brand']->id == '138' || $data['brand']->id == '494' || $data['brand']->id == Brand::KENKO_KENTEI_ID): ?>
        <!-- Start Visual Website Optimizer Asynchronous Code -->
        <script type='text/javascript'>
            var _vwo_code=(function(){
                var account_id=214575,
                    settings_tolerance=2000,
                    library_tolerance=2500,
                    use_existing_jquery=false,
// DO NOT EDIT BELOW THIS LINE
                    f=false,d=document;return{use_existing_jquery:function(){return use_existing_jquery;},library_tolerance:function(){return library_tolerance;},finish:function(){if(!f){f=true;var a=d.getElementById('_vis_opt_path_hides');if(a)a.parentNode.removeChild(a);}},finished:function(){return f;},load:function(a){var b=d.createElement('script');b.src=a;b.type='text/javascript';b.innerText;b.onerror=function(){_vwo_code.finish();};d.getElementsByTagName('head')[0].appendChild(b);},init:function(){settings_timer=setTimeout('_vwo_code.finish()',settings_tolerance);var a=d.createElement('style'),b='body{opacity:0 !important;filter:alpha(opacity=0) !important;background:none !important;}',h=d.getElementsByTagName('head')[0];a.setAttribute('id','_vis_opt_path_hides');a.setAttribute('type','text/css');if(a.styleSheet)a.styleSheet.cssText=b;else a.appendChild(d.createTextNode(b));h.appendChild(a);this.load('//dev.visualwebsiteoptimizer.com/j.php?a='+account_id+'&u='+encodeURIComponent(d.URL)+'&r='+Math.random());return settings_timer;}};}());_vwo_settings_timer=_vwo_code.init();
        </script>
        <!-- End Visual Website Optimizer Asynchronous Code -->
    <?php endif;?>
</head>

<body <?php if ($data['is_cmt_plugin_mode']): ?>class="popupAuth"<?php endif ?>>
<div id="fb-root"></div>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '<?php assign(config('@facebook.Admin.AppId')) ?>',
            xfbml      : true,
            version    : 'v2.1'
        });

        FB.XFBML.parse();

        FB.Event.subscribe('edge.create',
            function (url, html_element) {
                var action_url = '';
                var status = 1;
                if ($('.executeFbLikeActionForm').size() > 0) {
                    UserActionFacebookLikeService.executeFBLikeLogAction(html_element, action_url, status);
                } else {
                    action_url = '<?php assign(Util::rewriteUrl('messages', "api_execute_fb_like_action.json")); ?>';
                    UserActionEngagementService.executeFBLikeAction(html_element, action_url, status);
                }
            }
        );
    };

    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.async = true;
        js.src = "//connect.facebook.net/ja_JP/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/plusone.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
    !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
</script>
<?php if($data['isLoginAdmin'] && !$data['isOrderList']):?>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAdminHeader')->render(array('login_info' => $data))) ?>
<?php endif;?>

<?php if ($data['demo_info']['is_demo_cp']): ?>
<section class="demoMode">
    <h1 class="modeLabel">現在デモ公開中です</h1>
    <div class="modeDetail">
        <p><?php if (!$data['demo_info']['isHideDemoUrl']): ?>
            (<a href="<?php assign($data['demo_info']['demo_cp_url']) ?>"><?php assign($data['demo_info']['demo_cp_url']) ?></a>)<br>
            <?php endif; ?>
            本番公開時と同様にユーザーの画面遷移をご確認いただけます<br>
            <strong class="attention1">※各種SNSへのフォローやいいね！のアクションは実際に反映されますのでご注意ください。挙動確認時はテストアカウントをご利用ください。
                <br>※写真投稿/人気投票後のシェア・キャンペーンシェア・ツイート・リツイートは反映されません。
                <br>※キャンペーンを拡散する「このキャンペーンを友達に知らせよう」の欄も各SNSに反映されますのでご注意ください。
            </strong>
        </p>
        <?php if ($data['isLogin'] && !$data['demo_info']['isHideClearButton']): ?>
            <p class="btn1"><a href="javascript:void(0)" class="large2 resetOneDemoButton" data-cp-id="<?php assign($data['demo_info']["cp_id"]) ?>">自身の参加情報のクリア</a></p>
        <?php endif; ?>
        <!-- /.modeDetail --></div>
    <!-- /.demoMode --></section>
<?php write_html($this->parseTemplate('CpDemoConfirmBoxTemplate.php')) ?>
<?php endif; ?>
