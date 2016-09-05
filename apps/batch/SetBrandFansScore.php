<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.SetBrandFansScore');

$obj = new SetBrandFansScore($argv);
if (!($lock_file = Util::lockFile($obj))) return;
ini_set('memory_limit', '256M');
$obj->doProcess();
