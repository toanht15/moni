<?php
require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.GetUsersLikesFbPage');

$obj = new GetUserLikesFbPage();
if (!($lock_file = Util::lockFile($obj))) return;
$obj->doProcess();
