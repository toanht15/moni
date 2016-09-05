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

	<link rel="stylesheet" href="<?php assign($this->setVersion('/css/style.css'))?>">
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/admin.css'))?>">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

    <?php $favicon_url = $data['brand']->getFaviconUrl(); ?>
    <?php if ($favicon_url): ?>
        <link rel="icon" href="<?php assign($favicon_url); ?>">
    <?php else: ?>
        <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">
    <?php endif ?>

	<?php if($_GET['close']):?>
		<script type="text/javascript">
        $('.jsModalCont', parent.document).animate({
            top: -150,
            opacity: 0
        }, 300, function(){
            $(this).parents('.jsModal').fadeOut(300);
        }).css({
                display: 'none'
            });
    	<?php if($_GET['refreshTop']):?>
        parent.Brandco.helper.brandcoBlockUI();
        window.top.location.replace(window.top.location.href.split('?', 1)+'?mid=updated');
    	<?php endif;?>
		</script>
	<?php endif;?>
	<style type="text/css">
		.modalInner-large > header.innerLI-small {
            border-top: 10px solid <?php assign($data['brand']->getColorMain())?>;
        }
		/* panel title color */
        .contBoxMain h1 {
            background-color: <?php assign($data['brand']->getColorMain())?>;
            color: <?php assign($data['brand']->getColorText())?>;
        }

	</style>
    <base href="<?php assign(Util::getBaseUrl()) ?>" data-static-href="<?php assign(config('Static.Url')) ?>">
</head>
<body class="modalInnerBody">
