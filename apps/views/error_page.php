<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="description" content="description" />
<meta name="keywords" content="keyword" />

<title>お探しのページが見つかりませんでした</title>

<link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">

</head>

<body>
お探しのページが見つかりませんでした

<?php write_html( $this->parseTemplate('GoogleAnalytics.php', array("path" => "error_page"))); ?>
</body>
</html>