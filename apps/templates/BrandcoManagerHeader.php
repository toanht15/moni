<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<title><?php assign($data['title']) ?></title>

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php assign($this->setVersion('/manager/css/bootstrap.min.css'))?>">

	<!-- Custom styles for this template -->
	<link rel="stylesheet" href="<?php assign($this->setVersion('/manager/dashboard.css'))?>">
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">
</head>
<body>

	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/dashboard">monipla<strong>Manager</strong></a>
			</div>
			<div class="navbar-collapse collapse">
                <div class="navbar-text"><?php assign($data['managerAccount']->name)?> さん</div>
				<ul class="nav navbar-nav navbar-right">
<!--
					<li><a href="#">Dashboard</a></li>
					<li><a href="#">Settings</a></li>
					<li><a href="#">Profile</a></li>
					<li><a href="#">Help</a></li>
-->
                    <li><a href="<?php assign(Util::rewriteUrl( 'dashboard', 'logout' )); ?>">ログアウト</a></li>
				</ul>
			</div>
		</div>
	</div>
